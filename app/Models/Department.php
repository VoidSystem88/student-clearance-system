<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'staff_email',
        'staff_password',
        'is_active',
    ];

    protected $hidden = [
        'staff_password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationship: Isang department ay may maraming clearance requests
    public function clearanceRequests()
    {
        return $this->hasMany(ClearanceRequest::class);
    }

    // Relationship: Isang department ay may maraming approval logs
    public function approvalLogs()
    {
        return $this->hasMany(ApprovalLog::class);
    }
    // app/Models/Department.php
    public function requirements()
    {
        return $this->hasMany(DepartmentRequirement::class)->orderBy('sort_order');
    }

    public function getActiveRequirements()
    {
        return $this->requirements()->where('is_active', true)->get();
    }
}