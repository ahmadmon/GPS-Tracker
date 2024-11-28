<?php

namespace App\Http\Controllers;

use App\Facades\Acl;
use App\Http\Requests\VehicleRequest;
use App\Models\User;
use App\Models\Vehicle;

class VehicleController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Acl::authorize('vehicles-list');

        if ($this->role === 'user') {
            $vehicles = Vehicle::where('user_id', $this->user->id)
                ->with('device:id,name')
                ->cursor();

        } elseif ($this->role === 'manager') {
            $vehicles = Vehicle::with('user', 'device:id,name')
                ->whereIn('user_id', $this->userCompaniesSubsetsId->merge([$this->user->id]))
                ->cursor();

        } else {
            $vehicles = Vehicle::with('user', 'device:id,name')->cursor();
        }

        return view('vehicle.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Acl::authorize('create-vehicle');

        if ($this->role === 'manager') {
            $users = User::where('status', 1)->whereIn('id', $this->userCompaniesSubsetsId)->cursor();
        } else {
            $users = User::where('status', 1)->cursor();
        }

        return view('vehicle.create', [
            'users' => $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VehicleRequest $request)
    {
        Acl::authorize('create-vehicle');

        $validated = $request->validated();

        $validated['user_id'] = !isset($request->user_id) ? auth()->id() : $request->user_id;

        Vehicle::create($validated);

        return to_route('vehicle.index')->with('success-alert', 'وسیله نقلیه با موفقیت افزوده شد.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        Acl::authorize('edit-vehicle', $vehicle);

        if ($this->role === 'manager') {
            $users = User::where('status', 1)->whereIn('id', $this->userCompaniesSubsetsId)->cursor();
        } else {
            $users = User::where('status', 1)->cursor();
        }

        return view('vehicle.edit', [
            'vehicle' => $vehicle,
            'users' => $users
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VehicleRequest $request, Vehicle $vehicle)
    {
        Acl::authorize('edit-vehicle', $vehicle);

        $validated = $request->validated();
        $validated['user_id'] = !isset($request->user_id) ? auth()->id() : $request->user_id;

        $vehicle->update($validated);

        return to_route('vehicle.index')->with('success-alert', 'وسیله نقلیه با موفقیت ویرایش شد.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        Acl::authorize('delete-vehicle', $vehicle);

        $vehicle->delete();


        return back()->with('success-alert', 'وسیله نقلیه با موفقیت حذف گردید.');
    }

    public function changeStatus(Vehicle $vehicle)
    {
        Acl::authorize('edit-vehicle');

        $vehicle->status = $vehicle->status == 0 ? 1 : 0;
        $vehicle->save();

        return response()->json(['status' => true, 'data' => (bool)$vehicle->status]);
    }
}
