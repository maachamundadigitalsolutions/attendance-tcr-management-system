<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens; 

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',   // ✅ add this
        'name',
        'address',
        'phone',
        'email',
        'shirt_size',
        'tshirt_size',
        'trouser_size',
        'jeans_size',
        'dob',
        'doj',
        'education',
        'total_exp',
        'summary_exp',
        'emergency_contact',
        'product',
        'password',
        'role,'
    ];


    protected $casts = [
    'dob' => 'date',
    'doj' => 'date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }
}
