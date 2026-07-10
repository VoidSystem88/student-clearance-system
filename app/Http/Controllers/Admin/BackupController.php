<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class BackupController extends Controller
{
    public function downloadBackup()
    {
        $rootPath = base_path();
        $zipFileName = 'void-full-backup-' . date('Y-m-d-H-i-s') . '.zip';
        $zipPath = storage_path('app/' . $zipFileName);
        
        // Create storage/app folder if not exists
        if (!File::exists(storage_path('app'))) {
            File::makeDirectory(storage_path('app'), 0755, true);
        }
        
        // Increase execution time for large backups
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '512M');
        
        $zip = new ZipArchive();
        
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            
            // ✅ I-ADD LAHAT NG ROOT FILES
            $rootFiles = scandir($rootPath);
            foreach ($rootFiles as $file) {
                if ($file === '.' || $file === '..') continue;
                
                $filePath = $rootPath . '/' . $file;
                
                // Skip directories (separate handling)
                if (is_dir($filePath)) continue;
                
                // Skip huge files (>100MB)
                if (filesize($filePath) > 100 * 1024 * 1024) continue;
                
                $zip->addFile($filePath, $file);
            }
            
            // ✅ I-ADD LAHAT NG FOLDERS RECURSIVELY
            $excludeFolders = [
                'node_modules',           // Too large (npm packages)
                '.git',                   // Git history (not needed)
                'storage/logs',           // Laravel logs (can be huge)
                'storage/framework/cache', // Cache files
                'storage/framework/views', // Compiled views
                'storage/framework/sessions', // Session files
                'storage/debugbar',       // Debugbar files
            ];
            
            $allFolders = scandir($rootPath);
            foreach ($allFolders as $folder) {
                if ($folder === '.' || $folder === '..') continue;
                
                $folderPath = $rootPath . '/' . $folder;
                
                if (is_dir($folderPath)) {
                    // Skip excluded folders
                    if (in_array($folder, $excludeFolders)) continue;
                    
                    $this->addEntireFolder($zip, $folderPath, $folder, $excludeFolders);
                }
            }
            
            $zip->close();
            
            // Check file size
            $zipSize = filesize($zipPath);
            $zipSizeMB = round($zipSize / (1024 * 1024), 2);
            
            return response()->download($zipPath, $zipFileName)
                ->deleteFileAfterSend(true);
        }
        
        return back()->with('error', 'Failed to create backup!');
    }
    
    private function addEntireFolder($zip, $realPath, $zipPath, $excludeFolders)
    {
        // Create folder in zip
        $zip->addEmptyDir($zipPath);
        
        $items = scandir($realPath);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            
            $realItemPath = $realPath . '/' . $item;
            $zipItemPath = $zipPath . '/' . $item;
            
            if (is_dir($realItemPath)) {
                // Check kung excluded
                if (in_array($item, $excludeFolders)) continue;
                
                // Recursive call
                $this->addEntireFolder($zip, $realItemPath, $zipItemPath, $excludeFolders);
            } else {
                // Skip files larger than 50MB (except vendor - we want everything)
                $fileSize = filesize($realItemPath);
                if ($fileSize > 100 * 1024 * 1024 && strpos($zipItemPath, 'vendor/') === false) {
                    continue; // Skip non-vendor files >100MB
                }
                
                // Add the file
                $zip->addFile($realItemPath, $zipItemPath);
            }
        }
    }
}