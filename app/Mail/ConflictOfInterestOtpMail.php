<?php

namespace App\Mail;

use App\Models\BoardPaper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConflictOfInterestOtpMail extends Mailable implements ShouldQueue
{
    public $declaration;
    public $meeting;
    public $otp;

    public function __construct($declaration, $meeting, $otp)
    {
        $this->declaration = $declaration;
        $this->meeting = $meeting;
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->subject('OTP Confirmation for Conflict of Interest Declaration')
            ->view('emails.conflict_otp')
            ->with([
                'declaration' => $this->declaration,
                'meeting' => $this->meeting,
                'otp' => $this->otp,
            ]);
    }
}
