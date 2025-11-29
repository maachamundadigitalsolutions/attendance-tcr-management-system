<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AttendanceNotification extends Notification
{
    use Queueable;

    protected $type;
    protected $attendance;

    public function __construct($type, $attendance = null)
    {
        $this->type = $type;
        $this->attendance = $attendance;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return $this->formatData();
    }

    public function toArray($notifiable)
    {
        return $this->formatData();
    }

    protected function formatData()
    {
        if ($this->type === 'punch_in_reminder') {
            return [
                'title' => 'Punch In Reminder',
                'message' => 'Please punch in for your shift.',
            ];
        }

        return [
            'title' => 'Punch Out Reminder',
            'message' => 'You have completed 9 hours. Please punch out.',
            'attendance_id' => $this->attendance ? $this->attendance->id : null,
        ];
    }
}
