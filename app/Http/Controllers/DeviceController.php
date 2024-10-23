<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeviceRequest;
use App\Http\Services\Notify\SMS\SmsService;
use App\Models\Device;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $devices = Device::with(['user:id,name'])->orderByDesc('created_at')->cursor();

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
        $device = Device::find($id);
        $lastLocation = Trip::where('device_id', $device->id)->orderByDesc('id')->first();

        return view('devices.map', [
            'device' => $device,
            'lastLocation' => $lastLocation,
        ]);
    }

    public function location(string $id)
    {
        $device = Device::find($id);
        $lastLocation = Trip::where('device_id', $device->id)->orderByDesc('id')->first();

        if ($lastLocation) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'lat' => $lastLocation->lat,
                    'lng' => $lastLocation->long,
                    'name' => $lastLocation->name,
                    'speed' => $lastLocation->device_stats['data']['speed'],
                ]
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'data' => [],
                'message' => 'An error occurred!'
            ]);
        }
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


    public function deviceConnection(Device $device)
    {
        return view('devices.connect-to-device', [
            'device' => $device
        ]);
    }

    public function connectToDevice(Request $request, Device $device)
    {
        $request->validate([
            'command' => 'required|string'
        ]);

        $sms = new SmsService();
        $sms->setTo($device->phone_number);
        $sms->setText($request->command);
        $res = $sms->api();

        if ($res->getStatusCode() == 200) {
            return back()->with('success-alert', 'دستور با موفقیت برای دستگاه ارسال شد.');
        } else {
            return back()->with('error-alert', "خطایی به وجود آمده است!\nلطفا بعد از چند لحظه دوباره امتحان کنید");
        }
    }


}
