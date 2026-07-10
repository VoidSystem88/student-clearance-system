<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifiedExport extends Model
{
    protected $fillable = [
        'department_id', 'generated_by', 'filename', 'csv_data', 
        'event_name', 'total_records', 'export_date', 'status', 'expires_at'
    ];
    
    protected $casts = [
        'export_date' => 'date',
        'expires_at' => 'datetime',
    ];
    
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}