<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DepartmentRequirement extends Model
{
    use HasFactory;

    protected $table = 'department_requirements';

    protected $fillable = [
        'department_id',
        'requirement_name',
        'is_required',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}