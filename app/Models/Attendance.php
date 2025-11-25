<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'time',
        'status',
        'remarks',
        'photo_path',
        'is_late',   // 👈 add this
    ];

    protected $casts = [
        'is_late' => 'boolean', // 👈 ensures true/false stored and retrieved correctly
        'date' => 'date:d-m-Y',   // 👈 will return 25-11-2025      
        'time'    => 'datetime:H:i:s', // optional: cast time
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // links to users.id
    }
}
