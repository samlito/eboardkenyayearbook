<?php

namespace App\Http\Services\Twwda;

use App\Models\Twwda\TwwdaNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TwwdaNotificationService
{
    /**
     * Create an in-system notification and optionally send email.
     */
    public static function notify(
        int $userId,
        string $title,
        string $message,
        string $type = 'general',
        ?string $link = null,
        ?int $meetingId = null,
        ?string $email = null,
        ?string $emailView = null,
        array $emailData = []
    ): TwwdaNotification {
        $notification = TwwdaNotification::create([
            'id' => (string) Str::uuid(),
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
            'meeting_id' => $meetingId,
        ]);

        // Send email if address and view provided
        if ($email && $emailView) {
            try {
                Mail::send($emailView, array_merge($emailData, [
                    'notificationTitle' => $title,
                    'notificationMessage' => $message,
                    'actionUrl' => $link ? url($link) : null,
                ]), function ($mail) use ($email, $title) {
                    $mail->to($email)
                        ->subject(setting('email_subject_prefix', 'eBoard') . ': ' . $title);
                });

                $notification->update(['email_sent_at' => now()]);
            } catch (\Exception $e) {
                \Log::error('eBoard Email failed: ' . $e->getMessage());
            }
        }

        return $notification;
    }

    /**
     * Send notification to multiple users.
     */
    public static function notifyMany(
        array $userIds,
        string $title,
        string $message,
        string $type = 'general',
        ?string $link = null,
        ?int $meetingId = null
    ): void {
        foreach ($userIds as $userId) {
            self::notify($userId, $title, $message, $type, $link, $meetingId);
        }
    }

    /**
     * Get unread notification count for a user.
     */
    public static function unreadCount(int $userId): int
    {
        return TwwdaNotification::forUser($userId)->unread()->count();
    }

    /**
     * Get notifications for a user.
     */
    public static function forUser(int $userId, int $limit = 20)
    {
        return TwwdaNotification::forUser($userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Mark all notifications as read for a user.
     */
    public static function markAllRead(int $userId): void
    {
        TwwdaNotification::forUser($userId)
            ->unread()
            ->update(['read_at' => now()]);
    }
}
