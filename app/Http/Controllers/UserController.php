<?php

namespace App\Http\Controllers;

use App\Facades\Acl;
use App\Http\Requests\UserRequest;
use App\Http\Services\Notify\SMS\SmsService;
use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class UserController extends BaseController
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Acl::authorize('users-list');

        if ($this->role === 'manager') {
            $users = User::orderByDesc('id')
                ->whereIn('id', $this->userCompaniesSubsetsId)
                ->cursor();
        } else {
            $users = User::orderByDesc('id')->cursor();
        }


        return view('user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Acl::authorize('create-user');

        // generating random Password
        //----------------------------------------
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < 8; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        session(['generatedPass' => $randomString]);


        // getting Permissions and Role from DB
        //----------------------------------------
        $permissions = collect([]);
        $roles = collect([]);
        if (can('user-permissions')) {

            $permissions = Cache::remember('permissions-list', 60 * 60, fn() => Permission::all()
                ->groupBy('groupName')
                ->mapWithKeys(function ($permissions, $key) {
                    return [
                        $key => $permissions->map(fn($permission): Collection => collect([
                            'id' => $permission->id,
                            'persian_name' => $permission->persian_name,
                        ]))
                    ];
                }));

            $roles = Cache::remember('roles-list', 60 * 60, fn() => Role::all());
        }


        // Manager's Company and All Company
        //----------------------------------------
        if ($this->role === 'manager') {
            $companies = Company::where('user_id', auth()->id())->orderByDesc('id')->cursor();

        } else {
            $companies = Company::orderByDesc('id')->cursor();
        }


        return view('user.create', [
            'password' => $randomString,
            'roles' => $roles,
            'permissions' => $permissions,
            'companies' => $companies
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request, SmsService $smsService)
    {
        Acl::authorize('create-user');

        $validated = $request->validated();
        $validated['password'] = session('generatedPass');

        $user = User::create(Arr::except($validated, ['permissions', 'role']));


        if (isset($validated['role']) && isset($validated['permissions'])) {
            $user->roles()->syncWithoutDetaching([$validated['role']]);
            $user->permissions()->syncWithoutDetaching($validated['permissions']);
            $user->clearPermissionCache();
        }

        Company::find($request->company_id)->users()->attach($user->id);

        // Send Sms To User
        $smsService->setTo($user->phone);
        $smsService->setText("{$user->name} عزیز به سمفا خوش آمدید\nنام کاربری شما: {$user->phone}\nرمز عبور موقت شما: {$validated['password']}\nبرای ورود و تغییر رمز، به سایت مراجعه کنید.");
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
        $user = User::where('id', $id)->with(['devices', 'vehicles'])->first();

        Acl::authorize('show-user', $user);

        return view('user.show', [
            'user' => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        Acl::authorize('edit-user', $user);


        // getting Permissions and Role from DB
        //----------------------------------------
        $permissions = collect([]);
        $roles = collect([]);
        $userPermissions = [];
        if (can('user-permissions')) {

            $permissions = Cache::remember('permissions-list', 60 * 60, fn() => Permission::all()
                ->groupBy('groupName')
                ->mapWithKeys(function ($permissions, $key) {
                    return [
                        $key => $permissions->map(fn($permission): Collection => collect([
                            'id' => $permission->id,
                            'persian_name' => $permission->persian_name,
                        ]))
                    ];
                }));

            $userPermissions = $user->permissions->pluck('id')->toArray();

            $roles = Cache::remember('roles-list', 60 * 60, fn() => Role::all());
        }


        // Manager's Company and All Company
        //----------------------------------------
        if ($this->role === 'manager') {
            $companies = Company::where('user_id', auth()->id())->orderByDesc('id')->cursor();

        } else {
            $companies = Company::orderByDesc('id')->cursor();
        }

        return view('user.edit', [
            'user' => $user->load(['permissions:id,persian_name', 'roles']),
            'roles' => $roles,
            'permissions' => $permissions,
            'userPermissions' => $userPermissions,
            'companies' => $companies
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        Acl::authorize('edit-user', $user);

        $validated = $request->validated();

        $user->update(Arr::except($validated, ['permissions', 'role']));

        if (isset($validated['role']) && isset($validated['permissions'])) {
            $user->roles()->sync([$validated['role']]);
            $user->permissions()->sync($validated['permissions']);
            $user->clearPermissionCache();
        }

        Company::find($request->company_id)->users()->sync([$user->id]);


        return back();
//        return to_route('user.index')->with('success-alert', "کاربر '{$user->name}' با موفقیت ویرایش شد.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        Acl::authorize('delete-user', $user);

        $user->delete();

        return back()->with('success-alert', 'کاربر با موفقیت حذف گردید.');

    }
}
