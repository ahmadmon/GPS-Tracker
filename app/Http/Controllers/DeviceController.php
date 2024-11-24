<?php

namespace App\Http\Controllers;

use App\Facades\Acl;
use App\Http\Requests\DeviceRequest;
use App\Http\Requests\StoreSmsRequest;
use App\Http\Services\DeviceManager;
use App\Http\Services\Notify\SMS\SmsService;
use App\Models\Device;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Exception;

class DeviceController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Acl::authorize('devices-list');

        if ($this->role === 'user') {
            $devices = Device::where('user_id', $this->user->id)
                ->with('vehicle:id,name,license_plate')
                ->orderByDesc('created_at')
                ->cursor();

        } elseif ($this->role === 'manager') {
            $devices = Device::whereIn('user_id', $this->userCompaniesSubsetsId->merge([$this->user->id]))
                ->with(['user:id,name', 'vehicle:id,name,license_plate'])
                ->orderByDesc('created_at')
                ->cursor();

        } else {
            $devices = Device::with(['user:id,name', 'vehicle:id,name,license_plate'])
                ->orderByDesc('created_at')
                ->cursor();
        }


        return view('devices.index', compact('devices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Acl::authorize('create-device');

        if ($this->role === 'manager') {
            $users = User::where('status', 1)->whereIn('id', $this->userCompaniesSubsetsId)->cursor();
            $vehicles = Vehicle::where('status', 1)->whereIn('user_id', $this->userCompaniesSubsetsId)->cursor();
        } else {
            $users = User::where('status', 1)->cursor();
            $vehicles = Vehicle::where('status', 1)->cursor();
        }


        return view('devices.create', [
            'users' => $users,
            'vehicles' => $vehicles
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DeviceRequest $request)
    {
        Acl::authorize('create-device');

        $validated = $request->validated();
        $validated['user_id'] = $this->role === 'user' ? auth()->id() : $request->user_id;

        // Store the device record
        Device::create($validated);

        return to_route('device.index')->with('success-alert', 'دستگاه جدید با موفقیت افزوده شد.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort(404);
//        $device = Device::find($id);
//
//        return view('devices.map', [
//            'device' => $device,
//            'lastLocation' => $device->lastLocation(),
//        ]);
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
        Acl::authorize('edit-device', $device);

        if ($this->role === 'manager') {
            $users = User::where('status', 1)->whereIn('id', $this->userCompaniesSubsetsId)->cursor();
            $vehicles = Vehicle::where('status', 1)->whereIn('user_id', $this->userCompaniesSubsetsId)->cursor();
        } else {
            $users = User::where('status', 1)->cursor();
            $vehicles = Vehicle::where('status', 1)->cursor();
        }

        return view('devices.edit', [
            'users' => $users,
            'device' => $device,
            'vehicles' => $vehicles
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DeviceRequest $request, Device $device)
    {
        Acl::authorize('edit-device', $device);

        $validated = $request->validated();
        $validated['user_id'] = $this->role === 'user' ? auth()->id() : $request->user_id;

        $device->update($validated);

        return to_route('device.index')->with('success-alert', 'دستگاه با موفقیت ویرایش شد.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $device = Device::findOrFail($id);

        Acl::authorize('delete-device', $device);

        $device->delete();

        return back()->with('success-alert', 'دستگاه با موفقیت حذف گردید.');
    }


    public function deviceSetting(Device $device)
    {
        Acl::authorize('device-settings', $device);

        return view('devices.device-setting', [
            'device' => $device
        ]);
    }

    /**
     * @throws Exception
     */
    public function storeSMS(StoreSmsRequest $request, Device $device)
    {
        Acl::authorize('device-settings', $device);

        $request->validated();

        $params = [
            'apn' => $request->apn,
            'interval' => $request->interval,
            'password' => $request->password,
            'phones' => ($device->brand == 'sinotrack' || count($request->phones) == 1 || is_null($request->phones[1])) ? $request->phones[0] : implode(',', $request->phones)
        ];

        $deviceManager = new DeviceManager($device);
        $deviceBrand = $deviceManager->getDevice($device->brand->value);
        $command = $deviceBrand->getCommand($request->command, $params);

        if (isset($request->password)) {
            $device->update(['password' => $request->password]);
        }

        $sms = new SmsService();
        $sms->setTo($device->phone_number);
        $sms->setText($command);
        $res = $sms->api();
        dd($res);

        if ($res->getStatusCode() == 200) {
            return back()->with('success-alert', 'دستور با موفقیت برای دستگاه ارسال شد.');
        } else {
            return back()->with('error-alert', "خطایی به وجود آمده است!\nلطفا بعد از چند لحظه دوباره امتحان کنید.\nدر صورت مشاهده دوباره این پیغام لطفا با پشتیبانی تماس بگیرید.");
        }
    }


}
