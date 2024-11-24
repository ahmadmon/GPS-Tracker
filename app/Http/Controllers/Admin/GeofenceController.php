<?php

namespace App\Http\Controllers\Admin;

use App\Facades\Acl;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\GeofenceRequest;
use App\Models\Device;
use App\Models\Geofence;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class GeofenceController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Acl::authorize('geofences-list');

        if ($this->role === 'user') {
            $geofences = $this->user->geofences()
                ->orderByDesc('id')
                ->with('devices:id,name')
                ->cursor();
        } elseif ($this->role === 'manager') {
            $geofences = Geofence::whereHas('device.user', fn($q) => $q
                ->where('id', $this->user->id)
                ->orWhereIn('id', $this->userCompaniesSubsetsId)
            )->with(['user:id,name', 'devices:id,name'])
                ->orderByDesc('id')
                ->cursor();

        } else {
            $geofences = Geofence::with(['user:id,name', 'devices:id,name'])
                ->orderByDesc('id')
                ->cursor();
        }

        return view('admin.geofence.index', compact('geofences'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Acl::authorize('create-geofence');

        if ($this->role === 'user') {
            $devices = $this->user->devices()->cursor();

        } elseif ($this->role === 'manager') {
            $devices = Device::where('status', 1)
                ->whereIn('user_id', $this->userCompaniesSubsetsId->merge([$this->user->id]))
                ->cursor();

        } else {
            $devices = Device::where('status', 1)->cursor();

        }


        return view('admin.geofence.create', compact('devices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GeofenceRequest $request)
    {
        Acl::authorize('create-geofence');

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
        Acl::authorize('show-geofence');

        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Geofence $geofence)
    {
        Acl::authorize('edit-geofence', $geofence);

        if ($this->role === 'user') {
            $devices = $this->user->devices()->cursor();

        } elseif ($this->role === 'manager') {
            $devices = Device::where('status', 1)
                ->whereIn('user_id', $this->userCompaniesSubsetsId->merge([$this->user->id]))
                ->cursor();

        } else {
            $devices = Device::where('status', 1)->cursor();

        }

        return view('admin.geofence.edit', compact('geofence', 'devices'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GeofenceRequest $request, Geofence $geofence)
    {
        Acl::authorize('edit-geofence', $geofence);

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
        Acl::authorize('edit-geofence', $geofence);

        $geofence->delete();

        return back()->with('success-alert', "حصار جغرافیایی با موفقیت حذف گردید.");
    }
}
