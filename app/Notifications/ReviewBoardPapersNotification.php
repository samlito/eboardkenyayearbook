<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;



use App\Models\BoardMeeting;

class ReviewBoardPapersNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public $meeting;
    public $submittedBy;
    /**
     * Create a new notification instance.
     *
     * @return void
     */


    public function __construct0($meeting)
    {
        $this->meeting = $meeting;
    }
    public function __construct(BoardMeeting $meeting, $submittedBy)
    {
        $this->meeting = $meeting;
        $this->submittedBy = $submittedBy;
    }


    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail0($notifiable)
    {
        return (new MailMessage)
            ->subject('Board Papers Ready for Review')
            ->greeting('Dear CEO,')
            ->line('All assigned Division Heads have submitted their board papers for:')
            ->line('**' . $this->meeting->title . '**')
            ->action('Review Papers', url('/ceo/board-papers/' . $this->meeting->id))
            ->line('Please proceed to review and approve the papers.');
    }

    public function toArray0($notifiable)
    {
        return [
            'message' => 'Board papers for meeting "' . $this->meeting->title . '" are ready for review.',
            'meeting_id' => $this->meeting->id,
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Board Paper Submitted for Review')
            ->greeting('Dear CEO,')
            ->line("A board paper has been submitted by **{$this->submittedBy->name}** for:")
            ->line("**{$this->meeting->title}**")
            ->line("Scheduled on: " . \Carbon\Carbon::parse($this->meeting->date)->format('l, jS F Y'))
            ->action('Review Papers', url('/ceo/board-papers/' . $this->meeting->id))
            ->line('You may proceed to review the paper.');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "A board paper was submitted by {$this->submittedBy->name} for meeting '{$this->meeting->title}'.",
            'meeting_id' => $this->meeting->id,
        ];
    }
}
