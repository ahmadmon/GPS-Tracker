<?php

namespace App\Http\Controllers;

use App\Helpers\Hadis;
use App\Models\Config;
use App\Models\Device;
use App\Models\Geofence;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DashboardController extends BaseController
{
    public function index()
    {
        if($this->role === 'user' || $this->role === 'manager'){
            return to_route('map');
        }

        $entities = Cache::remember('dashboard-entities-count', 60 * 20, function () {
            return Collection::make([
                'devices_count' => Device::count(),
                'users_count' => User::count(),
                'vehicles_count' => Vehicle::count(),
                'geofence_count' => Geofence::count(),
                'in_active_devices_count' => Device::where('status', 0)->count(),
                'in_active_users_count' => User::where('status', 0)->count(),
                'topDeviceUsers' => User::where('user_type', 0)->withCount('devices')->orderByDesc('devices_count')->limit(5)->get()
            ]);
        });


        return view('dashboard.super-admin-dashboard', [
            'hadis' => Cache::remember('daily-hadis', 60 * 120, fn() => Config::where('key', 'daily-hadis')->first()),
            'entities' => $entities
        ]);
    }


    public function getAvgTotalDistance($days = 30)
    {

        $cacheKey = "total_distance_{$days}_" . now()->format('Y-m-d');

        return Cache::remember($cacheKey, now()->addHour(), function () use ($days) {
            $startDate = now()->subDays($days)->startOfDay();
            $endDate = now()->endOfDay();

            $dailyDistances = [];

            DB::table('trips')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select('device_id', 'lat', 'long', DB::raw('DATE(created_at) as trip_date'))
                ->orderBy('device_id')
                ->orderBy('trip_date')
                ->chunk(1000, function ($trips) use (&$dailyDistances) {
                    $groupedTrips = $trips->groupBy(['trip_date', 'device_id']);

                    foreach ($groupedTrips as $date => $devices) {
                        $dailyDistance = 0;

                        foreach ($devices as $deviceTrips) {
                            $previousTrip = null;

                            foreach ($deviceTrips as $trip) {
                                if ($previousTrip) {
                                    $distance = calculateHaversineDistance(
                                        $previousTrip->lat,
                                        $previousTrip->long,
                                        $trip->lat,
                                        $trip->long
                                    );
                                    $dailyDistance += $distance;
                                }
                                $previousTrip = $trip;
                            }
                        }
                        $dailyDistances[jalaliDate($date, format: "m/d")] = ($dailyDistances[jalaliDate($date, format: "m/d")] ?? 0) + intval($dailyDistance);
                    }
                });

            $totalDistance = array_sum($dailyDistances);
            $averageDistance = count($dailyDistances) > 0 ? $totalDistance / count($dailyDistances) : 0;

            return response()->json([
                'dailyAvgDistance' => $dailyDistances,
                'totalAvgDistance' => intval($averageDistance)
            ]);
        });

    }

}
