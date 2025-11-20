<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tcr extends Model
{
    use HasFactory;

    protected $fillable = ['tcr_no','user_id','status','sr_no','payment_term','amount','tcr_photo','payment_screenshot'];

    // Relation with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

