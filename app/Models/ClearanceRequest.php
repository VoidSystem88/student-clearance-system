<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClearanceRequest extends Model
{
    use HasFactory;

    protected $table = 'clearance_requests';

    protected $fillable = [
        'student_id',
        'department_id',
        'status',
        'attachment_path',
        'request_message',
        'is_manually_verified',
        'verified_list_match',
        'remarks',
        'submitted_at',
        'processed_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'processed_at' => 'datetime',
        'is_manually_verified' => 'boolean',
        'verified_list_match' => 'boolean',
    ];

    // ============ RELATIONSHIPS ============

    /**
     * Get the student who made this clearance request
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the department for this clearance request
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the approval log for this clearance request
     */
    public function approvalLog()
    {
        return $this->hasOne(ApprovalLog::class);
    }

    // ============ HELPER METHODS ============

    /**
     * Check if the request is approved
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the request is rejected
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if the request is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if student is in verified list
     */
    public function isInVerifiedList()
    {
        return $this->verified_list_match === true;
    }

    /**
     * Check if manually verified by staff
     */
    public function isManuallyVerified()
    {
        return $this->is_manually_verified === true;
    }

    /**
     * Check if has request message
     */
    public function hasRequestMessage()
    {
        return !empty($this->request_message);
    }

    /**
     * Check if has attachment
     */
    public function hasAttachment()
    {
        return !empty($this->attachment_path);
    }

    /**
     * Get attachment URL
     */
    public function getAttachmentUrlAttribute()
    {
        if (!$this->attachment_path) {
            return null;
        }
        
        $filename = basename($this->attachment_path);
        return url('/file/' . $filename);
    }

    /**
     * Get status badge class for HTML
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'approved' => 'bg-green-100 text-green-700',
            'rejected' => 'bg-red-100 text-red-700',
            'pending' => 'bg-yellow-100 text-yellow-700',
            default => 'bg-gray-100 text-gray-600',
        };
    }

    /**
     * Get status icon for HTML
     */
    public function getStatusIconAttribute()
    {
        return match($this->status) {
            'approved' => 'fa-check-circle',
            'rejected' => 'fa-times-circle',
            'pending' => 'fa-clock',
            default => 'fa-hourglass-start',
        };
    }

    /**
     * Get status label for display
     */
    public function getStatusLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    /**
     * Approve the request
     */
    public function approve($staffId, $remarks = null)
    {
        $this->update([
            'status' => 'approved',
            'processed_at' => now(),
            'remarks' => $remarks,
        ]);
        
        // Log the approval
        ApprovalLog::create([
            'clearance_request_id' => $this->id,
            'student_id' => $this->student_id,
            'department_id' => $this->department_id,
            'action' => 'approved',
            'remarks' => $remarks,
            'staff_email' => optional(User::find($staffId))->email,
        ]);
        
        // Check if student is fully cleared
        $this->checkFullClearance();
    }

    /**
     * Reject the request
     */
    public function reject($staffId, $remarks)
    {
        $this->update([
            'status' => 'rejected',
            'processed_at' => now(),
            'remarks' => $remarks,
        ]);
        
        // Log the rejection
        ApprovalLog::create([
            'clearance_request_id' => $this->id,
            'student_id' => $this->student_id,
            'department_id' => $this->department_id,
            'action' => 'rejected',
            'remarks' => $remarks,
            'staff_email' => optional(User::find($staffId))->email,
        ]);
    }

    /**
     * Check if student is fully cleared after approval
     */
    protected function checkFullClearance()
    {
        $student = User::find($this->student_id);
        
        if (!$student || $student->role !== 'student') {
            return;
        }
        
        $allRequests = ClearanceRequest::where('student_id', $this->student_id)->get();
        $allApproved = $allRequests->every(function ($request) {
            return $request->status === 'approved';
        });
        
        if ($allApproved && $allRequests->count() > 0) {
            $student->update([
                'is_cleared' => true,
                'cleared_at' => now(),
            ]);
        }
    }

    /**
     * Auto-approve if student is in verified list and has attachment
     */
    public function autoApproveIfEligible()
    {
        if ($this->verified_list_match && $this->attachment_path && $this->status === 'pending') {
            $this->update([
                'status' => 'approved',
                'is_manually_verified' => true,
                'processed_at' => now(),
            ]);
            
            $this->checkFullClearance();
            return true;
        }
        
        return false;
    }
}