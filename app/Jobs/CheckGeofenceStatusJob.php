<?php

namespace App\Jobs;

use App\Http\Services\Notify\SMS\SmsService;
use App\Models\Geofence;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class CheckGeofenceStatusJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue;

    protected $device;
    protected $trip;

    /**
     * Create a new job instance.
     */
    public function __construct($device, $trip)
    {
        $this->device = $device;
        $this->trip = $trip;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $geofences = Geofence::where('device_id', $this->device->id)->where('status', 1)->get();
        dd($geofences);
        foreach ($geofences as $fence) {

            if ($fence->isGeofenceActive()) continue;

            $isInGeofence = $this->isInGeofence($this->trip, $fence);

            $lastStatus = $fence->devices()->latest()->first()?->pivot;

            $shouldNotify = false;

            // types:  0 => leave , 1 => enter
            if (!$lastStatus) {
                $shouldNotify = ($fence->type === 1 && $isInGeofence) || ($fence->type === 0 && !$isInGeofence);
            } else {
                $wasInGeofence = $lastStatus->is_inside;

                if ($wasInGeofence !== $isInGeofence) {

                    if (($fence->type === 1 && $isInGeofence) || ($fence->type === 0 && !$isInGeofence)) {
                        $shouldNotify = true;
                    }

                }
            }
            dd('hi');

            DB::table('device_geofence')->insert([
                'device_id' => $this->device->id,
                'geofence_id' => $fence->id,
                'is_inside' => $isInGeofence,
                'lat' => $this->trip->lat,
                'long' => $this->trip->long,
                'created_at' => now()
            ]);

            if ($shouldNotify) {
                $this->sendSmsNotification($this->device, $this->trip, $fence);
            }


        }

    }


    private function isInGeofence($trip, $fence): bool
    {
        $polygon = $fence->points;

        return $this->pointInPolygon([$trip->lat, $trip->long], $polygon);
    }

    private
    function pointInPolygon($point, $polygon): bool
    {
        $x = $point[0];
        $y = $point[1];
        $inside = false;

        for ($i = 0, $j = count($polygon) - 1; $i < count($polygon); $j = $i++) {
            $xi = $polygon[$i][0];
            $yi = $polygon[$i][1];
            $xj = $polygon[$j][0];
            $yj = $polygon[$j][1];

            $intersect = (($yi > $y) != ($yj > $y)) &&
                ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }


    private
    function sendSmsNotification($device, $trip, $fence): void
    {
        $now = jalaliDate($trip->create_at, format: ' % d % B % Y ساعت H:i');

        if ($fence->type) {
            $message = "دستگاه {$device->name} از حصار {$fence->name} در تاریخ {$now} خارج شد.\nمشاهده موقعیت دستگاه:\n\nhttps://maps.google.com/?q={$trip->lat},{$trip->long}";
        } else {
            $message = "دستگاه {$device->name} به حصار {$fence->name} در تاریخ {$now} ورود کرد.\nمشاهده موقعیت دستگاه:\n\nhttps://maps.google.com/?q={$trip->lat},{$trip->long}";
        }

        $smsService = new SmsService();
        $smsService->setTo($device->user->phone);
        $smsService->setText($message);
    }

}
