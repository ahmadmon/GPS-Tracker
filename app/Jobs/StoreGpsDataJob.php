<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreGpsDataJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue;

    protected array $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $device = Device::where('serial', $this->data['device_id'])->first();

            if ($device) {
                DB::transaction(function () use ($device) {
                    Trip::create([
                        'device_id' => $device->id,
                        'user_id' => $device->user_id,
                        'vehicle_id' => $device?->vehicle_id,
                        'name' => jalaliDate(Carbon::now(), format: 'Y/m/d H:i:s'),
                        'lat' => $this->data['lat'],
                        'long' => $this->data['long'],
                        'device_stats' => json_encode($this->data),
                        'distance' => 0,
                        'start_at' => null,
                        'end_at' => null
                    ]);
                });
            }

        } catch (\Exception $e) {
            Log::error('Error storing trips Data: ' . $e->getMessage());
        }
    }

// calculating Distance between two points

//   private function haversine($lat1, $lon1, $lat2, $lon2) {
//        $earth_radius = 6371;
//
//        $dLat = deg2rad($lat2 - $lat1);
//        $dLon = deg2rad($lon2 - $lon1);
//
//        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
//        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
//
//        $distance = $earth_radius * $c;
//        return $distance;
//    }

}