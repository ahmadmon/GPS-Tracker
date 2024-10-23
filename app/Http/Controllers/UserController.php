<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Services\Notify\SMS\SmsService;
use App\Models\User;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::cursor();

        return view('user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < 8; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        session(['generatedPass' => $randomString]);

        return view('user.create', [
            'password' => $randomString
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request, SmsService $smsService)
    {
        $validated = $request->validated();
        $validated['password'] = session('generatedPass');

        $user = User::create($validated);

        // Send Sms To User
        $smsService->setTo($user->phone);
        $smsService->setText("{$user->name} عزیز به آرون خوش آمدید\nنام کاربری شما: {$user->phone}\nرمز عبور موقت شما: {$validated['password']}\nبرای ورود و تغییر رمز، به سایت مراجعه کنید.");
        $smsService->fire();

        //removing The session
        session()->forget('generatedPass');

        return to_route('user.index')->with('success-alert', "کاربر جدید با موفقیت ثبت نام شد.\nنام کاربری و رمز‌ عبور برای کاربر ارسال شد.");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {


        return view('user.show', [
            'user' => User::where('id', $id)->with(['devices', 'vehicles'])->first()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        $validated = $request->validated();

        $user->update($validated);

        return to_route('user.index')->with('success-alert', "کاربر '{$user->name}' با موفقیت ویرایش شد.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (auth()->user()->user_type == 1) {
            $user->delete();
            return back()->with('success-alert', 'کاربر جدید با موفقیت ثبت نام شد.');
        } else {
            return back()->with('error-alert', 'شما دسترسی ندارید!');
        }
    }
}
