<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'request_type', 'description', 'status', 'admin_notes', 'resolved_at'
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function getRequestTypeLabelAttribute()
    {
        $types = [
            'password_reset' => '🔐 Password Reset',
            'account_id_reset' => '🆔 Account ID Reset',
            'login_issue' => '🚪 Login Issue',
            'otp_issue' => '📧 OTP Issue',
            'account_activation' => '🔓 Account Activation',
        ];
        return $types[$this->request_type] ?? $this->request_type;
    }

    public function getStatusLabelAttribute()
    {
        $statuses = [
            'pending' => '⏳ Pending',
            'in_progress' => '🔄 In Progress',
            'resolved' => '✅ Resolved',
            'cancelled' => '❌ Cancelled',
        ];
        return $statuses[$this->status] ?? $this->status;
    }
}