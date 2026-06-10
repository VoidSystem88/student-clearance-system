<?php

namespace App\Mail;

use App\Models\User;
use App\Models\ClearanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClearanceStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $department;
    public $status;
    public $remarks;

    public function __construct(User $student, $department, $status, $remarks = null)
    {
        $this->student = $student;
        $this->department = $department;
        $this->status = $status;
        $this->remarks = $remarks;
    }

    public function envelope(): Envelope
    {
        $subject = $this->status === 'approved' 
            ? '✅ Clearance Request Approved' 
            : '❌ Clearance Request Rejected';
            
        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.clearance-status',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}