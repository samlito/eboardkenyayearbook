<?php

namespace App\Http\Services\Twwda;

use App\Models\Twwda\TwwdaConflictDeclaration;
use App\Models\Twwda\TwwdaMeeting;
use App\Models\Twwda\TwwdaMeetingAttendance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LiveMeetingService
{
    /**
     * Activate/start a meeting.
     */
    public function startMeeting(TwwdaMeeting $meeting): void
    {
        DB::transaction(function () use ($meeting) {
            $oldStatus = $meeting->status;

            $meeting->update([
                'is_started' => true,
                'started_at' => now(),
                'started_by' => Auth::id(),
                'status' => 'meeting_active',
                'current_stage' => 9,
            ]);

            // Notify all assigned officers and board members
            $assignedUserIds = $meeting->officerAssignments()->pluck('user_id')->unique()->toArray();
            TwwdaNotificationService::notifyMany(
                $assignedUserIds,
                'Meeting Has Started',
                "Meeting \"{$meeting->title}\" is now in progress.",
                'meeting_started',
                "/workflow/meetings/{$meeting->id}/live",
                $meeting->id
            );

            TwwdaAuditService::log(
                'meeting_started',
                'Stage 9: Live Meeting',
                TwwdaMeeting::class,
                $meeting->id,
                "Meeting started: {$meeting->title}",
                ['status' => $oldStatus, 'is_started' => false],
                ['status' => 'meeting_active', 'is_started' => true],
                $meeting->id
            );
        });
    }

    /**
     * Record attendance for a meeting participant.
     *
     * Data should contain: board_member_id (optional), user_id (optional),
     * attendee_name, attendee_designation, attendance_type (e.g. 'present', 'virtual', 'absent')
     */
    public function addAttendance(TwwdaMeeting $meeting, array $data): TwwdaMeetingAttendance
    {
        return DB::transaction(function () use ($meeting, $data) {
            $attendance = TwwdaMeetingAttendance::create([
                'meeting_id' => $meeting->id,
                'board_member_id' => $data['board_member_id'] ?? null,
                'user_id' => $data['user_id'] ?? null,
                'attendee_name' => $data['attendee_name'],
                'attendee_designation' => $data['attendee_designation'] ?? null,
                'attendance_type' => $data['attendance_type'] ?? 'board_member',
                'joined_at' => now(),
                'is_present' => true,
            ]);

            TwwdaAuditService::log(
                'attendance_recorded',
                'Stage 9: Live Meeting',
                TwwdaMeetingAttendance::class,
                $attendance->id,
                "Attendance recorded: {$data['attendee_name']} ({$attendance->attendance_type})",
                null,
                $attendance->toArray(),
                $meeting->id
            );

            return $attendance;
        });
    }

    /**
     * Submit a conflict of interest declaration.
     *
     * Data should contain: board_member_id, has_conflict, conflict_details (if applicable),
     * person_name, person_address, interest_nature, interest_value, interest_ceased_on, remarks
     */
    public function submitConflict(TwwdaMeeting $meeting, array $data): TwwdaConflictDeclaration
    {
        return DB::transaction(function () use ($meeting, $data) {
            $declaration = TwwdaConflictDeclaration::create([
                'meeting_id' => $meeting->id,
                'board_member_id' => $data['board_member_id'],
                'has_conflict' => $data['has_conflict'] ?? false,
                'conflict_details' => $data['conflict_details'] ?? null,
                'person_name' => $data['person_name'] ?? null,
                'person_address' => $data['person_address'] ?? null,
                'interest_nature' => $data['interest_nature'] ?? null,
                'interest_value' => $data['interest_value'] ?? null,
                'interest_ceased_on' => $data['interest_ceased_on'] ?? null,
                'remarks' => $data['remarks'] ?? null,
                'ip_address' => request()->ip(),
            ]);

            TwwdaAuditService::log(
                'conflict_declaration_submitted',
                'Stage 9: Live Meeting',
                TwwdaConflictDeclaration::class,
                $declaration->id,
                "Conflict declaration submitted by board member #{$data['board_member_id']}" .
                    ($data['has_conflict'] ? ' (conflict declared)' : ' (no conflict)'),
                null,
                ['has_conflict' => $data['has_conflict'], 'board_member_id' => $data['board_member_id']],
                $meeting->id
            );

            return $declaration;
        });
    }

    /**
     * Send OTP for conflict declaration verification.
     */
    public function sendOtp(TwwdaConflictDeclaration $declaration): void
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $declaration->update([
            'otp_code' => $otp,
            'otp_sent_at' => now(),
            'otp_verified_at' => null, // Reset any previous verification
        ]);

        // Send OTP via email to the board member
        $boardMember = $declaration->boardMember;
        if ($boardMember && $boardMember->email) {
            try {
                $meeting = $declaration->meeting;
                Mail::send('workflow.emails.conflict-otp', [
                    'otpCode' => $otp,
                    'boardMember' => $boardMember,
                    'meeting' => $meeting,
                    'notificationTitle' => $meeting ? $meeting->title : null,
                    'notificationMessage' => "Please use the following One-Time Password (OTP) to verify your Conflict of Interest declaration for the meeting: " . ($meeting ? $meeting->title : ''),
                ], function ($mail) use ($boardMember) {
                    $mail->to($boardMember->email)
                        ->subject(setting('email_subject_prefix', 'eBoard') . ': Conflict Declaration OTP');
                });
            } catch (\Exception $e) {
                \Log::error('eBoard Conflict OTP email failed: ' . $e->getMessage());
            }
        }

        TwwdaAuditService::log(
            'conflict_otp_sent',
            'Stage 9: Live Meeting',
            TwwdaConflictDeclaration::class,
            $declaration->id,
            "OTP sent for conflict declaration verification",
            null,
            ['otp_sent_at' => now()->toIso8601String()],
            $declaration->meeting_id
        );
    }

    /**
     * Verify OTP for a conflict declaration.
     *
     * @return bool Whether the OTP was valid
     */
    public function verifyOtp(TwwdaConflictDeclaration $declaration, string $code): bool
    {
        // Check OTP validity (within 10 minutes)
        if (
            $declaration->otp_code !== $code ||
            !$declaration->otp_sent_at ||
            $declaration->otp_sent_at->diffInMinutes(now()) > 10
        ) {
            TwwdaAuditService::log(
                'conflict_otp_failed',
                'Stage 9: Live Meeting',
                TwwdaConflictDeclaration::class,
                $declaration->id,
                "OTP verification failed for conflict declaration",
                null,
                ['attempted_at' => now()->toIso8601String()],
                $declaration->meeting_id
            );

            return false;
        }

        $declaration->update([
            'otp_verified_at' => now(),
        ]);

        TwwdaAuditService::log(
            'conflict_otp_verified',
            'Stage 9: Live Meeting',
            TwwdaConflictDeclaration::class,
            $declaration->id,
            "OTP verified successfully for conflict declaration",
            null,
            ['otp_verified_at' => now()->toIso8601String()],
            $declaration->meeting_id
        );

        return true;
    }

    /**
     * Sign a conflict declaration with a stylus signature.
     *
     * @param string $signature Base64-encoded signature image data
     */
    public function signDeclaration(TwwdaConflictDeclaration $declaration, string $signature): void
    {
        DB::transaction(function () use ($declaration, $signature) {
            $declaration->update([
                'signature' => $signature,
                'signed_at' => now(),
                'ip_address' => request()->ip(),
            ]);

            // ── Auto-mark attendance when conflict declaration is signed ──
            $meeting = $declaration->meeting ?? TwwdaMeeting::find($declaration->meeting_id);
            $boardMember = $declaration->boardMember;

            if ($meeting && $boardMember) {
                $existingAttendance = TwwdaMeetingAttendance::where('meeting_id', $meeting->id)
                    ->where('board_member_id', $boardMember->id)
                    ->first();

                if ($existingAttendance) {
                    // Already exists — ensure marked as present
                    if (!$existingAttendance->is_present) {
                        $existingAttendance->update([
                            'is_present' => true,
                            'joined_at' => $existingAttendance->joined_at ?? now(),
                        ]);
                    }
                } else {
                    // Create new attendance record automatically
                    TwwdaMeetingAttendance::create([
                        'meeting_id' => $meeting->id,
                        'board_member_id' => $boardMember->id,
                        'user_id' => $boardMember->user_id,
                        'attendee_name' => $boardMember->names,
                        'attendee_designation' => $boardMember->designation,
                        'attendance_type' => 'board_member',
                        'joined_at' => now(),
                        'is_present' => true,
                    ]);
                }
            }

            TwwdaAuditService::log(
                'conflict_declaration_signed',
                'Stage 9: Live Meeting',
                TwwdaConflictDeclaration::class,
                $declaration->id,
                "Conflict declaration signed by board member #{$declaration->board_member_id} — attendance auto-recorded",
                null,
                ['signed_at' => now()->toIso8601String()],
                $declaration->meeting_id
            );
        });
    }
}
