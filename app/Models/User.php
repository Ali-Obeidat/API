<?php

namespace App\Models;

use App\Notifications\MailResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Session\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;
use Twilio\Rest\Client;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function information()
    {
        return $this->hasOne(BasicInformation::class);
    }
    public function logins(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MtHulul::class)->orderBy('created_at', 'DESC');
    }
    public function quc()
    {
        return $this->hasOne(Quc::class);
    }
    public function accounts()
    {
        return $this->hasMany(UserAccounts::class);
    }
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function agreed()
    {
        if (!empty($this->quc)) {
            foreach ($this->quc as $value) {
                return $value;
                if ('1' == $value->agree) {
                    return true;
                }
                return false;
            }
        }
    }

    public function sendPasswordResetNotification($token )
    {

        $url = 'http://localhost:3000/auth/return-password?token=' . $token  ;

        $this->notify(new MailResetPasswordNotification($url));
    }
}
