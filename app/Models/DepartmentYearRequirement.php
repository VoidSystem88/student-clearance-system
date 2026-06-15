<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentYearRequirement extends Model
{
    protected $table = 'department_year_requirements';
    
    protected $fillable = [
        'department_id',
        'year_level',
        'requirement_name',
        'is_required',
        'sort_order',
        'is_active'
    ];
    
    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean'
    ];
    
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}