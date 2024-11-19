<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::with(['users', 'manager'])->orderByDesc('id')->cursor();

        return view('company.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $managers = User::where('status', 1)->whereIn('user_type', [1, 2])->cursor();

        return view('company.create', compact('managers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyRequest $request)
    {
        $validated = $request->validated();

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

        return view('company.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        $managers = User::where('status', 1)->whereIn('user_type', [1, 2])->cursor();

        return view('company.edit', compact('managers', 'company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyRequest $request, Company $company)
    {
        $validated = $request->validated();

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
        if (File::exists($company->logo)) {
            File::delete($company->logo);
        }
        $name = $company->name;
        $company->delete();

        return back()->with('success-alert', "سازمان {$name} با موفقیت حذف گردید");
    }

    /**
     * add simple user to company
     */
    public function addSubsets(Company $company)
    {
        $users = User::where([['status', 1], ['user_type', 0]])->orderByDesc('created_at')->cursor();

        return view('company.add-subsets', compact('users'));
    }

    public function storeSubsets()
    {

    }
}
