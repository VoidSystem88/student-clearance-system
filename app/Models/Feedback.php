<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    // ✅ IDAGDAG ITO - para malaman na ang table name ay 'feedbacks'
    protected $table = 'feedbacks';

    protected $fillable = [
        'user_id', 'rating', 'category', 'message', 'status', 'admin_response'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}