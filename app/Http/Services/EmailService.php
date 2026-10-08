<?php

namespace App\Http\Services;
use App\Mail\GenericEmail;
use Illuminate\Support\Facades\Mail;
class EmailService
{
//    public static function sendEmail0($to, $subject, $body)
//    {
//        try {
//            Mail::raw($body, function ($message) use ($to, $subject) {
//                $message->to($to)->subject($subject);
//            });
//
//            return true;
//        } catch (\Exception $e) {
//            \Lo::error("Email sending failed: " . $e->getMessage());
//            return false;
//        }
//    }

    public static function sendEmail($to, $subject, $body)
    {
        try {
            Mail::to($to)->send(new GenericEmail($subject, $body));
            return true;
        } catch (\Exception $e) {
            \Log::error("Email sending failed: " . $e->getMessage());
            return false;
        }
    }
}
