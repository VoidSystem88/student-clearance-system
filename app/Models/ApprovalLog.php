<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'clearance_request_id',
        'student_id',
        'department_id',
        'action',
        'remarks',
        'staff_email',
    ];

    // Relationship: Belongs to ClearanceRequest
    public function clearanceRequest()
    {
        return $this->belongsTo(ClearanceRequest::class);
    }

    // Relationship: Belongs to Student (User model)
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Relationship: Belongs to Department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}