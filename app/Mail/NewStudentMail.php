<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewStudentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $adminName;

    public function __construct($student, $adminName = 'Admin')
    {
        $this->student = $student;
        $this->adminName = $adminName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Student Registered - ' . $this->student->first_name . ' ' . $this->student->last_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-student',
        );
    }
}