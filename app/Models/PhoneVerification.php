<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PhoneVerification extends Model
{
    protected $fillable = [
        'phone',
        'code',
        'expires_at',
        'verified'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified' => 'boolean'
    ];

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return !$this->verified && !$this->isExpired();
    }

    public static function generateCode(string $phone): string
    {
        $code = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        static::create([
            'phone' => $phone,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        return $code;
    }

    public static function verify(string $phone, string $code): bool
    {
        $verification = static::where('phone', $phone)
            ->where('code', $code)
            ->where('verified', false)
            ->first();

        if ($verification && $verification->isValid()) {
            $verification->update(['verified' => true]);
            return true;
        }

        return false;
    }
}