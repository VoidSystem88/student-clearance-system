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
        'handler_name',  // ✅ IDAGDAG ITO
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
    
    public function requirements()
    {
        return $this->hasMany(DepartmentRequirement::class)->orderBy('sort_order');
    }

    public function getActiveRequirements()
    {
        return $this->requirements()->where('is_active', true)->get();
    }
    
    public function yearAssignments()
    {
        return $this->hasMany(DepartmentYearAssignment::class);
    }

    public function getAssignedYearLevels()
    {
        return $this->yearAssignments()->pluck('year_level')->toArray();
    }

    public function isRequiredForYear($yearLevel)
    {
        return $this->yearAssignments()
            ->where('year_level', $yearLevel)
            ->exists();
    }

    public static function getDepartmentsByYearLevel($yearLevel)
    {
        $allDepartments = self::where('is_active', true)->get();
        
        $yearMapping = [
            '1st Year' => ['Library', 'Accounting', 'Registrar', 'Guidance'],
            '2nd Year' => ['Library', 'Accounting', 'Registrar', 'Guidance', "Dean's Office"],
            '3rd Year' => ['Library', 'Accounting', 'Registrar', 'Guidance', "Dean's Office", 'Laboratory'],
            '4th Year' => ['Library', 'Accounting', 'Registrar', 'Guidance', "Dean's Office", 'Laboratory'],
        ];
        
        $allowedDeptNames = $yearMapping[$yearLevel] ?? $yearMapping['4th Year'];
        
        return $allDepartments->filter(function($dept) use ($allowedDeptNames) {
            return in_array($dept->name, $allowedDeptNames);
        });
    }
    public function yearRequirements()
{
    return $this->hasMany(DepartmentYearRequirement::class);
}

public function getRequirementsForYear($yearLevel)
{
    return $this->yearRequirements()
        ->where('year_level', $yearLevel)
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();
}
}