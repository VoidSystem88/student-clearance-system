<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class BackupController extends Controller
{
    // Show password form
    public function showPasswordForm()
    {
        return view('admin.backup-password');
    }
    
    // Verify password
    public function verifyPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string'
        ]);
        
        if (Hash::check($request->password, Auth::user()->password)) {
            Session::put('backup_authenticated', true);
            Session::put('backup_authenticated_at', now());
            return redirect()->route('admin.backup');
        }
        
        return redirect()->back()->with('error', 'Invalid password. Access denied.');
    }
    
    // Show backup page (protected)
    public function index()
    {
        $backups = [];
        
        // Create backups directory if not exists
        if (!is_dir(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0777, true);
        }
        
        // Get all backup files
        $files = glob(storage_path('app/backups/*.sql'));
        foreach($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'size' => filesize($file),
                'modified' => filemtime($file),
                'path' => $file
            ];
        }
        
        // Sort by modified date (newest first)
        usort($backups, function($a, $b) {
            return $b['modified'] - $a['modified'];
        });
        
        return view('admin.backup', compact('backups'));
    }
    
    // Create database backup (no additional password needed since page is already protected)
    public function create(Request $request)
    {
        try {
            // Create backups directory if not exists
            if (!is_dir(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0777, true);
            }
            
            $backupFile = $this->backupDatabasePHP();
            return redirect()->route('admin.backup')->with('success', 'Backup created successfully: ' . basename($backupFile));
        } catch (\Exception $e) {
            return redirect()->route('admin.backup')->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }
    
    // Download backup file
    public function download($filename)
    {
        $filepath = storage_path('app/backups/' . $filename);
        
        if (!file_exists($filepath)) {
            return redirect()->route('admin.backup')->with('error', 'Backup file not found');
        }
        
        return response()->download($filepath, $filename, [
            'Content-Type' => 'application/sql',
        ]);
    }
    
    // Delete backup file
    public function delete($filename)
    {
        $filepath = storage_path('app/backups/' . $filename);
        
        if (!file_exists($filepath)) {
            return redirect()->route('admin.backup')->with('error', 'Backup file not found');
        }
        
        unlink($filepath);
        return redirect()->route('admin.backup')->with('success', 'Backup file deleted successfully');
    }
    
    // Import database
    public function import(Request $request)
    {
        $request->validate([
            'sql_file' => 'required|file|mimes:sql,txt|max:20480'
        ]);
        
        try {
            $file = $request->file('sql_file');
            $content = file_get_contents($file->getPathname());
            
            $queries = $this->splitSqlQueries($content);
            
            DB::beginTransaction();
            
            $successCount = 0;
            $errorCount = 0;
            
            foreach ($queries as $query) {
                if (trim($query)) {
                    try {
                        DB::statement($query);
                        $successCount++;
                    } catch (\Exception $e) {
                        if (!str_contains($e->getMessage(), "Unknown table")) {
                            $errorCount++;
                            \Log::error('SQL Import Error: ' . $e->getMessage());
                        } else {
                            $successCount++;
                        }
                    }
                }
            }
            
            DB::commit();
            
            $importPath = storage_path('app/backups/imported');
            if (!is_dir($importPath)) {
                mkdir($importPath, 0777, true);
            }
            $fileName = 'imported_' . date('Y-m-d_H-i-s') . '.sql';
            $file->move($importPath, $fileName);
            
            return redirect()->route('admin.backup')->with('success', "Database imported successfully! {$successCount} queries executed.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.backup')->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
    
    // Split SQL file into individual queries
    private function splitSqlQueries($sql)
    {
        $queries = [];
        $currentQuery = '';
        $inString = false;
        $stringChar = '';
        $len = strlen($sql);
        
        for ($i = 0; $i < $len; $i++) {
            $char = $sql[$i];
            
            if (($char == "'" || $char == '"') && ($i == 0 || $sql[$i - 1] != '\\')) {
                if (!$inString) {
                    $inString = true;
                    $stringChar = $char;
                } elseif ($stringChar == $char) {
                    $inString = false;
                }
            }
            
            $currentQuery .= $char;
            
            if (!$inString && $char == ';') {
                $trimmed = trim($currentQuery);
                if (!empty($trimmed)) {
                    $queries[] = $trimmed;
                }
                $currentQuery = '';
            }
        }
        
        if (trim($currentQuery)) {
            $queries[] = trim($currentQuery);
        }
        
        return $queries;
    }
    
    // Backup using PHP
    private function backupDatabasePHP()
    {
        $tables = DB::select('SHOW TABLES');
        $dbname = config('database.connections.mysql.database');
        $tableKey = "Tables_in_{$dbname}";
        
        $sql = "-- =============================================\n";
        $sql .= "-- DATABASE BACKUP\n";
        $sql .= "-- Generated: " . now() . "\n";
        $sql .= "-- Database: {$dbname}\n";
        $sql .= "-- =============================================\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $sql .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $sql .= "SET AUTOCOMMIT = 0;\n";
        $sql .= "START TRANSACTION;\n\n";
        
        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $sql .= $createTable[0]->{'Create Table'} . ";\n\n";
            
            $rows = DB::table($tableName)->get();
            if (count($rows) > 0) {
                $rowCount = 0;
                foreach ($rows as $row) {
                    $columns = array_keys((array)$row);
                    $values = array_map(function($value) {
                        if ($value === null) return 'NULL';
                        return "'" . addslashes($value) . "'";
                    }, array_values((array)$row));
                    
                    $sql .= "INSERT INTO `{$tableName}` (`" . implode("`, `", $columns) . "`) VALUES (" . implode(", ", $values) . ");\n";
                    $rowCount++;
                    
                    if ($rowCount % 100 == 0) {
                        $sql .= "\n";
                    }
                }
                $sql .= "\n";
            }
        }
        
        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        $sql .= "COMMIT;\n";
        
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "backup_{$timestamp}.sql";
        $filepath = storage_path("app/backups/{$filename}");
        
        file_put_contents($filepath, $sql);
        
        return $filepath;
    }
    
    // Get file size in human readable format
    public static function formatSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }
}