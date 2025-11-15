<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    // 👇 Primary key settings
    protected $primaryKey = 'user_id';   // primary key column name
    public $incrementing = false;        // not auto-increment
    protected $keyType = 'string';       // column type is string

   protected $fillable = [
    'user_id', 'date', 'status', 'remarks', 'photo_path'
];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}


