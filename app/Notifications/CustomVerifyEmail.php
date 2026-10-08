<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends VerifyEmail
{
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject(setting('institution_acronym', 'ORG') . ' eBoard — Verify Your Email Address')
            ->view('emails.verify-email', [
                'user' => $notifiable,
                'verificationUrl' => $verificationUrl,
            ]);
    }
}
