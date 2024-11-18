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
                $trip = DB::transaction(function () use ($device) {
                    return Trip::create([
                        'device_id' => $device->id,
                        'user_id' => $device->user_id,
                        'vehicle_id' => $device?->vehicle_id,
                        'name' => jalaliDate(Carbon::now(), format: 'Y/m/d H:i:s'),
                        'lat' => $this->data['lat'],
                        'long' => $this->data['long'],
                        'device_stats' => json_encode($this->data),
                    ]);
                });

                CheckGeofenceStatusJob::dispatch($device, $trip);

            }

        } catch (\Exception $e) {
            Log::error('Error storing trips Data: ' . $e->getMessage());
        }
    }

}
