<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Referrer extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function user(Type $var = null)
    {
        return $this->belongsTo(User::class);
    }
    public function getReferralLinkAttribute(): string
    {
        return $this->referral_link = route('register', ['ref' => Auth::user() ->id]);
    }
}
