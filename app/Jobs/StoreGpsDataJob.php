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
            $now = Carbon::now();

            $device = Device::where('serial', $this->data['device_id'])->first();

            if ($device) {
//                $prevPoint = [
//                    'lat' => $device->lastLocation()?->lat,
//                    'lng' => $device->lastLocation()?->long,
//                    'datetime' => $device->lastLocation()?->created_at,
//                    'speed' => json_decode($device->lastLocation()?->device_stats)?->speed
//                ];
//
//                $currentPoint = [
//                    'lat' => $this->data['lat'],
//                    'lng' => $this->data['long'],
//                    'datetime' => Carbon::make($this->data['datetime']),
//                    'speed' => $this->data['speed']
//                ];

//                if ($this->isValid($prevPoint, $currentPoint)) {

                $device->update(['connected_at' => $now]);
                $trip = DB::transaction(function () use ($device, $now) {
                    return Trip::create([
                        'device_id' => $device->id,
                        'user_id' => $device->user_id,
                        'vehicle_id' => $device?->vehicle_id,
                        'name' => jalaliDate($this->data['received_at'], format: 'Y/m/d H:i:s'),
                        'lat' => $this->data['lat'],
                        'long' => $this->data['long'],
                        'device_stats' => json_encode($this->data),
                        'created_at' => $this->data['received_at']
                    ]);
                });

                CheckGeofenceStatusJob::dispatch($device, $trip);
//                }
            }

        } catch (\Exception $e) {
            Log::error('Error storing trips Data: ' . $e->getMessage());
        }
    }


    private function isValid($prevPoint, $currentPoint): bool
    {
        //if is the first data point.
        if (empty($prevPoint)) return true;

        $distance = round(calculateHaversineDistance($prevPoint['lat'], $prevPoint['lng'], $currentPoint['lat'], $currentPoint['lng']), 5);
        $timeDiff = ($currentPoint['datetime']->getTimestamp() - $prevPoint['datetime']->getTimestamp());
        $timeDiffHours = $timeDiff / 3600;

        // if device is off or Internet not available
        // after 30 min -> the first data point is valid
        if ($timeDiff > 1800) return true;

        if ($timeDiff <= 0 || $timeDiff > 50) return false;

        // check stop
        if ($distance <= 0.2 && abs($currentPoint['speed']) <= 10) return false;


        // calculate Logical distance if speed over 120 km/h | default is 1 km/h
        $maxPossibleDistance = $currentPoint['speed'] >= 120 ? 120 * $timeDiffHours : 1;
        if ($distance > $maxPossibleDistance) return false;


        $calculatedSpeed = $distance / $timeDiffHours;
        if ($currentPoint['speed'] > 300 || $calculatedSpeed > 300) return false;


        $validSpeed = min($currentPoint['speed'], $calculatedSpeed);
        $prevSpeed = $prevPoint['speed'];
        $acceleration = abs($validSpeed - $prevSpeed) / $timeDiffHours;
        if ($acceleration > 10) return false;

        return true;
    }

}
