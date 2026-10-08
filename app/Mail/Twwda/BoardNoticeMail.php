<?php

namespace App\Mail\Twwda;

use App\Models\BoardMember;
use App\Models\Twwda\TwwdaBoardNotice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BoardNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    public TwwdaBoardNotice $notice;
    public ?BoardMember $member;
    public bool $isReminder;

    public function __construct(TwwdaBoardNotice $notice, ?BoardMember $member = null, bool $isReminder = false)
    {
        $this->notice = $notice;
        $this->member = $member;
        $this->isReminder = $isReminder;
    }

    public function build()
    {
        $notice = $this->notice;
        $notice->load('meeting.boardCommittee');

        // Resolve board members for addressee display in the PDF
        $boardMembers = BoardMember::all();

        // Generate PDF on-the-fly
        $pdf = Pdf::loadView('workflow.board-notices.pdf', [
            'notice'       => $notice,
            'boardMembers' => $boardMembers,
        ]);

        $pdf->setPaper('A4', 'portrait');

        // Build the filename
        $refClean = preg_replace('/[^A-Za-z0-9\-_]/', '_', $notice->reference_number ?? 'Board_Notice');
        $filename = 'Board_Notice_' . $refClean . '.pdf';

        $subjectLine = $this->isReminder
            ? 'REMINDER: ' . ($notice->subject ?? 'Board Meeting Notice')
            : ($notice->subject ?? 'Board Meeting Notice');

        return $this->subject(setting('email_subject_prefix', 'eBoard') . ': ' . $subjectLine)
            ->view('workflow.emails.board-notice-sent', [
                'notice'               => $notice,
                'member'               => $this->member,
                'notificationTitle'    => $subjectLine,
                'notificationMessage'  => $this->isReminder
                    ? 'This is a reminder for the upcoming board meeting. Please find the official notice attached.'
                    : 'Please find attached the official board meeting notice. Kindly review and plan to attend.',
                'actionUrl'            => route('board.portal.notice.show', $notice),
            ])
            ->attachData($pdf->output(), $filename, [
                'mime' => 'application/pdf',
            ]);
    }
}
