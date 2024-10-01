<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialLogin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function toProvider($driver)
    {
        return Socialite::driver($driver)->redirect();
    }

    public function handleCallBack($driver)
    {
        $user = Socialite::driver($driver)->user();

        $user_account = SocialLogin::where('provider', $driver)
            ->where('provider_token', $user->getId())
            ->first();

        if ($user_account) {
            Auth::login($user_account->user);

            session()->regenerate();

            return to_route('home');
        }

        $db_user = User::where('email', $user->getEmail())->first();

        if (!$db_user) {
            $db_user = User::create([
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'email_verified_at' => Carbon::now(),
                'password' => bcrypt(rand(1000, 9999)),
            ]);

        }
        SocialLogin::create([
            'provider' => $driver,
            'provider_token' => $user->getId(),
            'user_id' => $db_user->id
        ]);

        Auth::login($db_user);

        session()->regenerate();

        return to_route('home');
    }
}
