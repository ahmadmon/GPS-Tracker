<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\Notify\SMS\SmsService;
use App\Models\User;
use App\Traits\VerificationCode;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VerifyVerificationCodeController extends Controller
{
    use VerificationCode;

    public function verification(string $phone, SmsService $smsService)
    {
        $user = User::where('phone', $phone)->first();
        if (!$user) {
            return to_route('register')->with('error-alert', 'همچین کاربری پیدا نشد. لطفا ثبت نام کنید.');
        }

        $otpModel = $this->generateOtp($user->phone, $smsService);
        $seconds = isset($otpModel) ? Carbon::parse($otpModel->expired_at)->diffInSeconds(Carbon::now()) : -180;
        $duration = (int)number_format($seconds) * -1;


        return view('auth.otp-verification', ['user' => $user, 'duration' => $duration]);
    }

    public function verify(Request $request, User $user)
    {
        $request->validate([
            'otp_code' => 'required|numeric|digits:4|exists:verification_codes,otp'
        ]);

        $typeNumber = $this->verifyOtp($request->otp_code, $user->id, $request->recovery);


        return match ($typeNumber) {
            '0' => back()->withErrors(['otp_code' => 'کد ورود نادرست است.']),
            '1' => back()->withErrors(['otp_code' => 'کد ورود منقضی شده است, کد جدید ارسال شد.']),
            '2' => to_route('password.reset'),
            '3' => redirect()->intended(route('home', absolute: false)),
            default => back()->withErrors(['otp_code' => 'خطایی به وجود آمده است,لطفا بعدا تلاش کنید!']),
        };

    }


}
