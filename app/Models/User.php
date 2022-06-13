<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Nova\Actions\Actionable;
use Laravel\Nova\Auth\Impersonatable;

class User extends Authenticatable
{
    use Impersonatable, Actionable, HasApiTokens, HasFactory, Notifiable;
    //use Mailable;
    public function getEmailField(): string
    {
        return 'email';
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'active',
        'phonenumber_last_verification',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'phonenumber',
        'phonenumber_wrong_tries',
        'support_user',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'admin',
    ];

    public static function isAdmin()
    {
        return true;
    }

}
 
