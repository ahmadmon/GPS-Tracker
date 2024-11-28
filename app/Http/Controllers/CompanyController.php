<?php

namespace App\Http\Controllers;

use App\Facades\Acl;
use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class CompanyController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Acl::authorize('companies-list');

        if ($this->role === 'manager') {
            $companies = Company::where('user_id', $this->user->id)
                ->with(['users', 'manager'])
                ->orderByDesc('id')
                ->cursor();
        } else {
            $companies = Company::with(['users', 'manager'])->orderByDesc('id')->cursor();
        }


        return view('company.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Acl::authorize('create-company');

        $managers = User::where('status', 1)->where('user_type', 3)->cursor();

        return view('company.create', compact('managers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyRequest $request)
    {
        Acl::authorize('create-company');

        $validated = $request->validated();
        $validated['user_id'] = is_null($request->user_id) ? $this->user->id : $request->user_id;

        if ($request->hasFile('logo')) {
            $imageName = uniqid() . '-' . pathinfo($request->file('logo')->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';
            $destinationPath = public_path('uploads/company/');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $savePath = "uploads/company/$imageName";
            Image::read($request->file('logo'))->toWebp()->save($savePath);
            $validated['logo'] = $savePath;
        }

        Company::create($validated);

        return to_route('company.index')->with('success-alert', 'سازمان جدید با موفقیت ایجاد شد.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $company = Company::where('id', $id)->with(['manager', 'users'])->first();

        Acl::authorize('show-company', $company);

        return view('company.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        Acl::authorize('edit-company', $company);

        $managers = User::where('status', 1)->where('user_type', 3)->cursor();

        return view('company.edit', compact('managers', 'company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyRequest $request, Company $company)
    {
        Acl::authorize('edit-company', $company);

        $validated = $request->validated();
        $validated['user_id'] = is_null($request->user_id) ? $this->user->id : $request->user_id;

        if ($request->hasFile('logo')) {
            //Remove  existing file
            if (File::exists($company->logo)) File::delete($company->logo);

            // Create destination directory and imageName
            $imageName = uniqid() . '-' . pathinfo($request->file('logo')->getClientOriginalName(), PATHINFO_FILENAME) . '.webp';
            $destinationPath = public_path('uploads/company/');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // save the file
            $savePath = "uploads/company/$imageName";
            Image::read($request->file('logo'))->toWebp()->save($savePath);
            $validated['logo'] = $savePath;
        } else {
            $validated['logo'] = $company->logo;
        }

        $company->update($validated);


        return to_route('company.index')->with('success-alert', "سازمان {$company->name} با موفقیت ویرایش شد.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        Acl::authorize('delete-company', $company);

        if (File::exists($company->logo)) {
            File::delete($company->logo);
        }
        $name = $company->name;
        $company->delete();

        return back()->with('success-alert', "سازمان {$name} با موفقیت حذف گردید");
    }

    public function manageSubsets(Company $company)
    {
        Acl::authorize('manage-subsets', $company);

        return view('company.manage-subsets', compact('company'));
    }

    public function removeSubsets(Company $company, $userId)
    {
        Acl::authorize('manage-subsets', $company);

        $company->users()->detach($userId);
        return to_route('company.show', $company->id)->with('success-alert', 'کاربر با موفقیت از لیست زیرمجموعه هایتان حذف شد.');
    }

    public function changeStatus(Company $company)
    {
        Acl::authorize('edit-company');

        $company->status = $company->status == 0 ? 1 : 0;
        $company->save();

        return response()->json(['status' => true, 'data' => (bool)$company->status]);
    }
}
