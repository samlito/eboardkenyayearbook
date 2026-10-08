<?php

namespace App\Mail;

use App\Models\BoardMeeting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
class TenDayBoardMeetingInvitationMail extends Mailable
{
    public $meeting;

    public function __construct(BoardMeeting $meeting)
    {
        $this->meeting = $meeting;
    }

    public function build()
    {
        $pdf = Pdf::loadView('backend.actual_meetings.partials.ten_preview_notice_pdf', [
            'meeting' => $this->meeting,
            'orderedPapers' => $this->meeting->officialNotice10->agenda_order ?? []
        ]);

        return $this->subject('Official Board Meeting Invitation')
            ->markdown('emails.board_meeting_invitation')
            ->with(['meeting' => $this->meeting])
            ->attachData($pdf->output(), 'BoardMeetingInvitation.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
