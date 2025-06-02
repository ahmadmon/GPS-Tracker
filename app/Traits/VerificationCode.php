<?php

namespace App\Traits;


use App\Jobs\SendSms;
use App\Models\User;
use App\Models\VerificationCode as OtpCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait VerificationCode
{
    public function generateOtp(string $mobile, $existsUser = null)
    {
        $user = User::where('phone', $mobile)->first() ?? $existsUser;

        $verificationCode = OtpCode::where('user_id', $user->id)->latest()->first();
        $now = Carbon::now();

        if ($verificationCode && $now->isBefore($verificationCode->expired_at)) {
            return $verificationCode;
        }

        $verificationCode = OtpCode::create([
            'user_id' => $user->id,
            'otp' => rand(1234, 9999),
            'expired_at' => $now->addMinutes(value: 2)
        ]);

        $message = "سمفا - سامانه هوشمند رهیابی GPS\nرمز ورود: {$verificationCode->otp}\n";
        SendSms::dispatch($mobile, $message);


        return $verificationCode;
    }

    public function verifyOtp(string $otp, string $user_id, $recovery = null): string
    {
        $verificationCode = OtpCode::where('user_id', $user_id)->where('otp', $otp)->first();

        $now = Carbon::now();
        if (!$verificationCode) {
            return '0';
        } elseif ($now->isAfter($verificationCode->expired_at)) {
            return '1';
        }

        $user = User::find($user_id);

        if ($user) {
            $verificationCode->update(['expired_at' => $now]);

            if ($recovery) {
                session()->put('verified_phone', $user->phone);
                return '2';
            }


            $user->update(['phone_verified_at' => $now]);

            Auth::login($user);

            session()->regenerate();

            return '3';
        } else {
            return '0';
        }


    }
}

