<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeviceRequest;
use App\Models\Device;
use App\Models\User;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $devices = Device::with(['user'])->orderByDesc('created_at')->cursor();

        return view('devices.index', compact('devices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('devices.create', [
            'users' => User::where([['status', 1], ['user_type', 0]])->cursor()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DeviceRequest $request)
    {
        $validated = $request->validated();

        // Store the device record
        Device::create($validated);

        return to_route('device.index')->with('success-alert', 'دستگاه جدید با موفقیت افزوده شد.');
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
    public function edit(Device $device)
    {
        return view('devices.edit', [
            'users' => User::where([['status', 1], ['user_type', 0]])->cursor(),
            'device' => $device,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DeviceRequest $request, Device $device)
    {
        $validated = $request->validated();

        $device->update($validated);

        return to_route('device.index')->with('success-alert', 'دستگاه با موفقیت ویرایش شد.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!auth()->check() && auth()->user()->user_type != 1) {
            return false;
        }
        Device::destroy($id);
        return back()->with('success-alert', 'دستگاه با موفقیت حذف گردید.');
    }
}