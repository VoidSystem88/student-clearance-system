<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VerifiedStudent extends Model
{
    use HasFactory;

    protected $table = 'verified_students';

    protected $fillable = [
        'department_id',
        'student_id',
        'student_name',
        'verified_by',
        'verified_at',
        'is_active',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // ============ RELATIONSHIPS ============

    /**
     * Get the department this verified student belongs to
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the staff who verified this student
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ============ HELPER METHODS ============

    /**
     * Check if verified student is active
     */
    public function isActive()
    {
        return $this->is_active === true;
    }

    /**
     * Deactivate this verified student
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Activate this verified student
     */
    public function activate()
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Get full display name with ID
     */
    public function getDisplayNameAttribute()
    {
        return "{$this->student_name} ({$this->student_id})";
    }
    public function officer()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}