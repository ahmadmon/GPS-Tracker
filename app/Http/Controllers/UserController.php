<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Services\Notify\SMS\SmsService;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderByDesc('id')->cursor();

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

        $permissions = Cache::remember('permissions-lits', 60 * 60, fn() => Permission::all()
            ->groupBy('groupName')
            ->mapWithKeys(function ($permissions, $key) {
                return [
                    $key => $permissions->map(fn($permission): Collection => collect([
                        'id' => $permission->id,
                        'persian_name' => $permission->persian_name,
                    ]))
                ];
            }));



        return view('user.create', [
            'password' => $randomString,
            'roles' => Cache::remember('roles-list', 60 * 60, fn() => Role::all()),
            'permissions' => $permissions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request, SmsService $smsService)
    {
        $validated = $request->validated();
        $validated['password'] = session('generatedPass');

        $user = User::create(Arr::except($validated, ['permissions', 'role']));

        $user->roles()->syncWithoutDetaching([$validated['role']]);
        $user->permissions()->syncWithoutDetaching($validated['permissions']);
        $user->clearPermissionCache();

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
        $permissions = Cache::remember('permissions-lits', 60 * 60, fn() => Permission::all()
            ->groupBy('groupName')
            ->mapWithKeys(function ($permissions, $key) {
                return [
                    $key => $permissions->map(fn($permission): Collection => collect([
                        'id' => $permission->id,
                        'persian_name' => $permission->persian_name,
                    ]))
                ];
            }));


        return view('user.edit', [
            'user' => $user->load(['permissions:id,persian_name', 'roles']),
            'roles' => Cache::remember('roles-list', 60 * 60, fn() => Role::all()),
            'permissions' => $permissions,
            'userPermissions' => $user->permissions->pluck('id')->toArray()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        $validated = $request->validated();

        $user->update(Arr::except($validated, ['permissions', 'role']));

        $user->roles()->sync([$validated['role']]);
        $user->permissions()->sync($validated['permissions']);
        $user->clearPermissionCache();

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
