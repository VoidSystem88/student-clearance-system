<?php

namespace App\Helpers;

use App\Models\VisitorTracking;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;

class TrackingHelper
{
    public static function trackVisitor(Request $request, $networkType = null)
    {
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());
        
        // Detect device type
        $deviceType = 'desktop';
        if ($agent->isMobile()) {
            $deviceType = 'mobile';
        } elseif ($agent->isTablet()) {
            $deviceType = 'tablet';
        } elseif ($agent->isDesktop()) {
            $deviceType = 'desktop';
        }
        
        // Get device model (limited)
        $deviceModel = $agent->device();
        
        // Get OS
        $os = $agent->platform();
        
        // Get browser
        $browser = $agent->browser();
        
        // Get IP
        $ip = $request->ip();
        
        // Optional: Get location from IP (using ip-api.com)
        $location = self::getLocationFromIp($ip);
        
        return VisitorTracking::create([
            'ip_address' => $ip,
            'device_type' => $deviceType,
            'device_model' => $deviceModel ?: 'Unknown',
            'os' => $os,
            'browser' => $browser,
            'network_type' => $networkType,
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'page_visited' => $request->fullUrl(),
            'country' => $location['country'] ?? null,
            'city' => $location['city'] ?? null,
        ]);
    }
    
    private static function getLocationFromIp($ip)
    {
        try {
            $response = @file_get_contents("http://ip-api.com/json/{$ip}");
            if ($response) {
                $data = json_decode($response, true);
                if ($data && $data['status'] === 'success') {
                    return [
                        'country' => $data['country'],
                        'city' => $data['city']
                    ];
                }
            }
        } catch (\Exception $e) {
            // Ignore location errors
        }
        return ['country' => null, 'city' => null];
    }
}