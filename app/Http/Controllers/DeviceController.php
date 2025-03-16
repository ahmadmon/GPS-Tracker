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
use Illuminate\Support\Facades\Log;

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
            'passStatus' => $request->passStatus ? 'on' : 'off',
            'phones' => $this->checkPhone($request->phones, $device),
            'other' => $request->other,
            'mode' => $request->mode
        ];

        $deviceManager = new DeviceManager($device);
        $deviceBrand = $deviceManager->getDevice($device->brand->value);
        $command = $deviceBrand->getCommand($request->command, $params);


        $sms = new SmsService();
        $sms->setTo($device->phone_number);
        $sms->setText($command);
        $res = $sms->api();

        if ($res->getStatusCode() == 200) {
            if ((isset($request->passStatus) && (bool)$request->passStatus === true) || isset($request->password)) {
                $pass = is_null($device->password) ? '000000' : $request->password;
                $device->update(['password' => $pass]);
            } elseif ((isset($request->passStatus) && (bool)$request->passStatus === false)) {
                $device->update(['password' => null]);
            }


            return back()->with('success-alert', 'دستور با موفقیت برای دستگاه ارسال شد.');
        } else {
            Log::error("Error Sending Msg by device {$device->serial}: ", [$res->original['error']]);
            return back()->with('error-alert', "خطایی به وجود آمده است!\nلطفا بعد از چند لحظه دوباره امتحان کنید.\nدر صورت مشاهده دوباره این پیغام لطفا با پشتیبانی تماس بگیرید.");
        }
    }

    private function checkPhone($phones, $device)
    {
        $result = null;

        if (is_array($phones)) {
            if (
                $device->brand == 'sinotrack' ||
                count($phones) == 1 ||
                (array_key_exists(1, $phones) && is_null($phones[1]))
            ) {
                $result = $phones[0];
            } else {
                $result = implode(',', $phones);
            }
        }

        return $result;
    }

    public function changeStatus(Device $device)
    {
        Acl::authorize('edit-device');

        $device->status = $device->status == 0 ? 1 : 0;
        $device->save();

        return response()->json(['status' => true, 'data' => (bool)$device->status]);
    }


}
