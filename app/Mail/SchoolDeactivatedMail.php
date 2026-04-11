<?php

namespace App\Mail;

use App\Models\School;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SchoolDeactivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public School $school) {}

    public function build()
    {
        return $this->subject('Your SchoolFeed account has been deactivated')
            ->view('emails.schools.deactivated', [
                'school' => $this->school,
            ]);
    }
}
