<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorTracking extends Model
{
    protected $table = 'visitor_tracking';
    
    protected $fillable = [
        'ip_address', 'device_type', 'device_model', 'os', 'browser',
        'network_type', 'user_agent', 'referer', 'page_visited', 'country', 'city'
    ];
}