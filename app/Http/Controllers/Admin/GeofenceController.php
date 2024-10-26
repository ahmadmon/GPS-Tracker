<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GeofenceRequest;
use App\Models\Device;
use App\Models\Geofence;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class GeofenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $geofences = Geofence::with(['user', 'devices'])->cursor();

        return view('admin.geofence.index', compact('geofences'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $devices = Device::where('status', 1)->cursor();

        return view('admin.geofence.create', compact('devices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GeofenceRequest $request)
    {
        $validated = $request->validated();
        $validated['shape'] = json_decode($validated['geometry'])->shape;
        $validated['points'] = json_decode($validated['geometry'])->latlng;

        if (isset($validated['time_restriction'])) {
            $validated['start_time'] = Carbon::parse($validated['start_time'])->toTimeString();
            $validated['end_time'] = Carbon::parse($validated['end_time'])->toTimeString();
        } else {
            $validated['start_time'] = null;
            $validated['end_time'] = null;
        }

        Geofence::create(Arr::except($validated, 'geometry'));

        return to_route('geofence.index')->with('success-alert', 'حصار جغرافیایی با موفقیت تعریف شد.');
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
    public function edit(Geofence $geofence)
    {
        $devices = Device::where('status', 1)->cursor();

        return view('admin.geofence.edit', compact('geofence', 'devices'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GeofenceRequest $request, Geofence $geofence)
    {
        $validated = $request->validated();
        $validated['shape'] = json_decode($validated['geometry'])->shape;
        $validated['points'] = json_decode($validated['geometry'])->latlng;

        if (isset($validated['time_restriction'])) {
            $validated['start_time'] = Carbon::parse($validated['start_time'])->toTimeString();
            $validated['end_time'] = Carbon::parse($validated['end_time'])->toTimeString();
        } else {
            $validated['start_time'] = null;
            $validated['end_time'] = null;
        }
        $geofence->update(Arr::except($validated, 'geometry'));

        return to_route('geofence.index')->with('success-alert', "حصار جغرافیایی {$geofence->name} با موفقیت تعریف شد.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Geofence $geofence)
    {
        $geofence->delete();

        return back()->with('success-alert', "حصار جغرافیایی با موفقیت حذف گردید.");
    }
}
