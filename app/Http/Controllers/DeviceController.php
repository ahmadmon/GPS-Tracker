<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeviceRequest;
use App\Http\Requests\StoreSmsRequest;
use App\Http\Services\DeviceManager;
use App\Http\Services\Notify\SMS\SmsService;
use App\Models\Device;
use App\Models\Trip;
use App\Models\User;
use Exception;

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

        $deviceManager = new DeviceManager($device);
        $deviceBrand = $deviceManager->getDevice($device->brand->value);
        $parsedData = $deviceBrand->parseData($device,'78781f122c0610060220cc03d4202c0580da0824146f01b02315860114fe121224540d0a78781f122c061006022ecc03d41d010580e37024146d01b02315860114fe121224540d0a78781f122c0610060239cc03d41b900580e96017146d01b02315860114fe121224540d0a78781f122c0610060301cc03d419a00580ee481e146c01b02315860114fe121224540d0a78781f122c0610060303cc03d418f40580f0b819146b01b02315860114fe121224540d0a78781f122c0610060306cc03d418540580f2f01e146c01b02315860114fe121224540d0a78781f122c0610060309cc03d417bc0580f52822146d01b02315860114fe121224540d0a78781f122c061006030fcc03d416c00580f8f024146d01b02315860114fe121224540d0a78781f122c0610060315cc03d415880580fd4024146d01b02315860114fe121224540d0a78781f122c0610060318cc03d414d80580ff7024146e01b02315860114fe121224540d0a78781f122c061006031ecc03d413c0058103c024146c01b02315860114fe121224540d0a78781f122c0610060322cc03d412f40581069024146d01b02315860114fe121224540d0a78781f122c0610060328cc03d411b005810ad824146d01b02315860114fe121224540d0a78781f122c061006032ecc03d4108c05810f3824146c01b02315860114fe121224540d0a78781f122c0610060330cc03d40fdc0581116824146d01b02315860114fe121224540d0a78781f122c0610060334cc03d40f3c058113a824146d01b02315860114fe121224540d0a78781f122c0610060337cc03d40e94058115e024146d01b02315860114fe121224540d0a78781f122c061006033acc03d40df80581182024146c01b02315860114fe121224540d0a78781f122c0610060401cc03d40d6005811a6024146c01b02315860114fe121224540d0a78781f122c0610060408cc03d40c1c05811f3024146c01b02315860114fe121224540d0a');

        dd($parsedData);

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


    public function deviceSetting(Device $device)
    {
        return view('devices.device-setting', [
            'device' => $device
        ]);
    }

    /**
     * @throws Exception
     */
    public function storeSMS(StoreSmsRequest $request, Device $device)
    {
        $request->validated();

        $params = [
            'apn' => $request->apn,
            'interval' => $request->interval,
            'password' => $request->password,
            'phone' => $request->phone
        ];

        if (isset($request->password)) {
            $device->update(['password' => $request->password]);
        }

        $deviceManager = new DeviceManager($device);
        $deviceBrand = $deviceManager->getDevice($device->brand->value);
        $command = $deviceBrand->getCommand($request->command, $params);

        dd($command);


        $sms = new SmsService();
        $sms->setTo($device->phone_number);
        $sms->setText($command);
        $res = $sms->api();

        if ($res->getStatusCode() == 200) {
            return back()->with('success-alert', 'دستور با موفقیت برای دستگاه ارسال شد.');
        } else {
            return back()->with('error-alert', "خطایی به وجود آمده است!\nلطفا بعد از چند لحظه دوباره امتحان کنید");
        }
    }


}
