<?php

namespace App\Console\Commands;

use App\Models\VisitorTracking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncTrackingLogs extends Command
{
    protected $signature = 'tracking:sync';
    protected $description = 'Sync tracking.log entries to visitor_tracking table';

    public function handle()
    {
        $logFile = storage_path('logs/tracking.log');
        
        if (!file_exists($logFile)) {
            $this->error('Log file not found!');
            return;
        }
        
        $content = file_get_contents($logFile);
        $lines = explode("\n", $content);
        $count = 0;
        
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            // Parse log line: [2026-05-28 01:19:03] IP: 202.61.110.117 | Referer: ... | UA: ...
            preg_match('/\[(.*?)\]\s+IP:\s+(\d+\.\d+\.\d+\.\d+)\s+\|\s+Referer:\s+(.*?)\s+\|\s+UA:\s+(.*)/', $line, $matches);
            
            if (count($matches) >= 5) {
                $ip = $matches[2];
                $referer = $matches[3];
                $userAgent = $matches[4];
                $createdAt = $matches[1];
                
                // Check if already exists
                $exists = VisitorTracking::where('ip_address', $ip)
                    ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime($createdAt . ' -1 minute')))
                    ->exists();
                
                if (!$exists) {
                    // Get location from IP
                    $location = $this->getLocationFromIp($ip);
                    
                    VisitorTracking::create([
                        'ip_address' => $ip,
                        'device_type' => $this->getDeviceType($userAgent),
                        'os' => $this->getOS($userAgent),
                        'browser' => $this->getBrowser($userAgent),
                        'network_type' => $this->getNetworkType($userAgent),
                        'user_agent' => $userAgent,
                        'referer' => $referer,
                        'page_visited' => $referer,
                        'country' => $location['country'] ?? null,
                        'city' => $location['city'] ?? null,
                        'latitude' => $location['lat'] ?? null,
                        'longitude' => $location['lon'] ?? null,
                        'isp' => $location['isp'] ?? null,
                        'created_at' => $createdAt,
                    ]);
                    $count++;
                }
            }
        }
        
        $this->info("Synced {$count} new entries to visitor_tracking table.");
    }
    
    private function getLocationFromIp($ip)
    {
        try {
            $json = @file_get_contents("http://ip-api.com/json/{$ip}");
            $data = json_decode($json, true);
            if ($data && $data['status'] === 'success') {
                return [
                    'country' => $data['country'],
                    'city' => $data['city'],
                    'lat' => $data['lat'],
                    'lon' => $data['lon'],
                    'isp' => $data['isp'],
                ];
            }
        } catch (\Exception $e) {}
        return [];
    }
    
    private function getDeviceType($ua)
    {
        if (preg_match('/(mobile|iphone|android|blackberry|windows phone)/i', $ua)) return 'mobile';
        if (preg_match('/(tablet|ipad|kindle)/i', $ua)) return 'tablet';
        return 'desktop';
    }
    
    private function getOS($ua)
    {
        if (preg_match('/Windows NT 10/i', $ua)) return 'Windows 10';
        if (preg_match('/Windows NT 11/i', $ua)) return 'Windows 11';
        if (preg_match('/Mac OS X/i', $ua)) return 'macOS';
        if (preg_match('/iPhone OS/i', $ua)) return 'iOS';
        if (preg_match('/Android/i', $ua)) return 'Android';
        if (preg_match('/Linux/i', $ua)) return 'Linux';
        return 'Unknown';
    }
    
    private function getBrowser($ua)
    {
        if (preg_match('/Edg/i', $ua)) return 'Edge';
        if (preg_match('/Chrome/i', $ua)) return 'Chrome';
        if (preg_match('/Firefox/i', $ua)) return 'Firefox';
        if (preg_match('/Safari/i', $ua)) return 'Safari';
        if (preg_match('/FBAN|FBAV/i', $ua)) return 'Facebook App';
        return 'Unknown';
    }
    
    private function getNetworkType($ua)
    {
        if (preg_match('/FBAN|FBAV|FB_IAB/i', $ua)) return 'wifi';
        return 'unknown';
    }
}