<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View|RedirectResponse
    {
        if (!session()->has('verified_phone')) {
            return to_route('password.request')->with('error-alert', 'لطفا شماره موبایل خود را تایید کنید.');
        }

        return view('auth.reset-password', ['request' => $request, 'phone' => session('verified_phone')]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'phone' => ['required', 'numeric', 'digits:11', 'exists:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return to_route('login')->with('error-alert', 'اطلاعات اشتباه بود لطفا مجددا تلاش کنید!');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        Auth::login($user, remember: true);

        $request->session()->regenerate();

        return to_route('home');

    }
}
