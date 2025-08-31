<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Allotment;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string> 
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'job', // nullable for admin
        'salary', // nullable for admin
        'expenditure',
        'budget',
        'role', // 'user' or 'admin'
        'profile_pending_approval',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function allotments()
    {
        return $this->hasMany(Allotment::class);
    }
    public function savingsApplications()
    {
        return $this->hasMany(\App\Models\SavingsApplication::class);
    }
}
