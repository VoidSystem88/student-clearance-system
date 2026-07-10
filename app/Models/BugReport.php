<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BugReport extends Model
{
    protected $fillable = [
        'name', 'email', 'student_id', 'type', 'message', 
        'browser_info', 'url', 'status', 'admin_response'
    ];
}