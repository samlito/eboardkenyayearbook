<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BoardMeetingNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $meeting;
    public $user;

    public function __construct($meeting, $user)
    {
        $this->meeting = $meeting;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject("Board Meeting Notice: {$this->meeting->title}")
            ->view('emails.board_meeting_notice');
    }
}
