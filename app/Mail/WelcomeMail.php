<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

   public function build()
{
    return $this->from(config('mail.from.address'), config('mail.from.name'))
                ->subject('Welcome to ' . config('app.name'))
                ->view('emails.new_student');  // Gamitin ang existing NewStudentMail view
}
}