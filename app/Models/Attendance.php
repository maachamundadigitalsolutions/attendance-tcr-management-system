<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id', 'date', 'time_in', 'time_out',
        'in_photo_path', 'out_photo_path',
         'status', 'remarks', 'working_hours', 'status',
    ];

    protected $casts = [
        'date' => 'date:d-m-Y',   // 👈 will return 25-11-2025      
        'time_in'    => 'datetime:H:i:s', // optional: cast time
        'time_out'    => 'datetime:H:i:s', // optional: cast time
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // links to users.id
    }
}
