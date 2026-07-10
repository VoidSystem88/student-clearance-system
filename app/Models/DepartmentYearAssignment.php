<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentYearAssignment extends Model
{
    use HasFactory;

    protected $table = 'department_year_assignments';

    protected $fillable = [
        'department_id',
        'year_level',
    ];

    protected $casts = [
        'department_id' => 'integer',
    ];

    // Relationship: Belongs to Department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}