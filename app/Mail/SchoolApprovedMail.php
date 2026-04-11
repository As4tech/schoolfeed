<?php

namespace App\Mail;

use App\Models\School;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SchoolApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public School $school,
        public ?User $adminUser = null
    ) {}

    public function build()
    {
        return $this->subject('Your SchoolFeed account has been approved')
            ->view('emails.schools.approved', [
                'school' => $this->school,
                'adminUser' => $this->adminUser,
            ]);
    }
}
