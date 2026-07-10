<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $table = 'reminders';

    protected $fillable = [
        'title',
        'message',
        'target_role',
        'department_id',
        'start_date',
        'end_date',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Scope for active reminders
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            });
    }

    // Get target audience text
    public function getTargetTextAttribute()
    {
        $targets = [
            'staff' => 'Department Staff',
            'officer' => 'Officers',
            'both' => 'Staff & Officers',
        ];
        return $targets[$this->target_role] ?? $this->target_role;
    }

    // Get status badge
    public function getStatusBadgeAttribute()
    {
        if (!$this->is_active) {
            return '<span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Inactive</span>';
        }
        
        $now = now();
        if ($this->start_date && $now->lt($this->start_date)) {
            return '<span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Scheduled</span>';
        }
        if ($this->end_date && $now->gt($this->end_date)) {
            return '<span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Expired</span>';
        }
        return '<span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>';
    }
}