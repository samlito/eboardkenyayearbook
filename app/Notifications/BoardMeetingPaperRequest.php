<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BoardMeetingPaperRequest extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($meeting)
    {
        $this->meeting = $meeting;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Action Required: Board Paper Submission for Upcoming Meeting')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('You have been requested to prepare and submit board papers for the upcoming board meeting.')
            ->line('**Meeting Title:** ' . $this->meeting->title)
            ->line('**Meeting Date:** ' . $this->meeting->date?->format('d M Y'))
            ->line('Please log in to the Board Management System to update or upload your board papers.')
            ->action('Log In to the System', url('/login'))
            ->line('We appreciate your timely submission to facilitate review and preparation for the meeting.');
    }


    public function toArray($notifiable)
    {
        return [
            'message' => 'You have been requested to submit board papers for meeting: ' . $this->meeting->title,
            'meeting_id' => $this->meeting->id
        ];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */

}
