<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use App\Notifications\AttendanceNotification;
use Carbon\Carbon;

class AttendanceReminders extends Command
{
    protected $signature = 'attendance:reminders';
    protected $description = 'Send attendance reminders (Punch In / Punch Out)';

    public function handle()
    {
        $now = now('Asia/Kolkata');
        $today = $now->toDateString();
        $this->info("=== Checking reminders at ".$now." ===");

        // Punch In window: 9 AM to 11 AM
        $punchInStart = Carbon::parse($today.' 09:00:00', 'Asia/Kolkata');
        $punchInEnd   = Carbon::parse($today.' 11:00:00', 'Asia/Kolkata');

        // Punch Out window: 7 PM to 9 PM
        $punchOutStart = Carbon::parse($today.' 19:00:00', 'Asia/Kolkata');
        $punchOutEnd   = Carbon::parse($today.' 21:00:00', 'Asia/Kolkata');

        $users = User::all();

        foreach ($users as $user) {
            $attendance = Attendance::where('user_id', $user->id)
                ->where('date', $today)
                ->first();

            // 🔔 Punch In reminder (between 9–11 AM if not punched in)
            if ($now->between($punchInStart, $punchInEnd) && !$attendance) {
                $user->notify(new AttendanceNotification('punch_in_reminder'));
                $this->info("👉 Punch In reminder sent to user ".$user->id);
            }

            // 🔔 Punch Out reminder (between 7–9 PM if not punched out)
            if ($attendance && $attendance->time_in && !$attendance->time_out) {
                if ($now->between($punchOutStart, $punchOutEnd)) {
                    $user->notify(new AttendanceNotification('punch_out_reminder', $attendance));
                    $this->info("👉 Punch Out reminder sent to user ".$user->id);
                }
            }
        }
    }
}
