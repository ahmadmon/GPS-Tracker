<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeUserPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index', [
            'user' => auth()->user()
        ]);
    }

    public function changePassword(ChangeUserPasswordRequest $request)
    {
        $request->validated();

        $user = auth()->user();
        if (Hash::check($request->old_password, $user->password)) {
            $user->password = Hash::make($request->password);
            $user->save();

            return to_route('profile.index')->with('success-alert', 'گذرواژه با موفقیت بروزرسانی شد.');
        } else {
            return to_route('profile.index')->withErrors(['old_password' => 'گذرواژه قبلی نادرست است.']);
        }
    }


    public function forgotPassword()
    {
        auth()->logout();

        return to_route('password.request');
    }
}
