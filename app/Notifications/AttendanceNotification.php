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
        return ['database']; // only in-app notifications
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
                'message' => 'Please punch in between 9–11 AM.',
            ];
        }

        return [
            'title' => 'Punch Out Reminder',
            'message' => 'Please punch out between 7–9 PM.',
            'attendance_id' => $this->attendance ? $this->attendance->id : null,
        ];
    }
}
