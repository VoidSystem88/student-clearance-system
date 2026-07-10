<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'account_id',
        'student_id',
        'first_name',
        'last_name',
        'email',
        'course',
        'password',
        'is_cleared',
        'cleared_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_cleared' => 'boolean',
        'cleared_at' => 'datetime',
    ];

    // Relationship: Isang student ay may maraming clearance requests
    public function clearanceRequests()
    {
        return $this->hasMany(ClearanceRequest::class);
    }

    // Relationship: Isang student ay may maraming approval logs
    public function approvalLogs()
    {
        return $this->hasMany(ApprovalLog::class);
    }

    // Helper: Kunin ang full name
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}