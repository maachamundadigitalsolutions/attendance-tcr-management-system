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

        $workStart = Carbon::parse($today.' 09:00:00', 'Asia/Kolkata');
        $workEnd   = Carbon::parse($today.' 21:00:00', 'Asia/Kolkata');

        if ($now->lt($workStart) || $now->gt($workEnd)) {
            $this->info("Outside work window, skipping reminders.");
            return;
        }

        $users = User::all();

        foreach ($users as $user) {
            $attendance = Attendance::where('user_id', $user->id)
                ->where('date', $today)
                ->first();

            // Punch In reminder
            if ($now->greaterThanOrEqualTo($workStart) && !$attendance) {
                $user->notify(new AttendanceNotification('punch_in_reminder'));
                $this->info("👉 Punch In reminder sent to user ".$user->id);
            }

            // Punch Out reminder
            if ($attendance && $attendance->time_in && !$attendance->time_out) {
                $start = Carbon::createFromFormat('H:i:s', $attendance->time_in, 'Asia/Kolkata');
                $minutesWorked = $start->diffInMinutes($now);
                $hoursWorked = $minutesWorked / 60;

                $this->info("User ".$user->id." has worked ".$hoursWorked." hours.");

                if ($hoursWorked >= 9) {
                    $user->notify(new AttendanceNotification('punch_out_reminder', $attendance));
                    $this->info("👉 Punch Out reminder sent to user ".$user->id);
                }
            }
        }
    }
}
