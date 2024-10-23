<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehicleRequest;
use App\Models\User;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehicles = Vehicle::with('user')->cursor();

        return view('vehicle.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vehicle.create', [
            'users' => User::where([['status', 1], ['user_type', 0]])->cursor()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VehicleRequest $request)
    {
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        return view('vehicle.edit', [
            'vehicle' => $vehicle,
            'users' => User::where([['status', 1], ['user_type', 0]])->cursor()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VehicleRequest $request, Vehicle $vehicle)
    {
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
        Vehicle::destroy($id);

        return back()->with('success-alert', 'وسیله نقلیه با موفقیت حذف گردید.');
    }
}
