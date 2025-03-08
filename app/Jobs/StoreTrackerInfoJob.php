<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\DeviceStatus;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreTrackerInfoJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue;

    /**
     * Create a new job instance.
     * @param array $data
     * @return void
     */
    public function __construct(protected array $data)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try{

            $device = Device::where('serial', $this->data()->device_id)->first();

            if (!$device) {
                return;
            }
            DB::transaction(function () use ($device) {
                return DeviceStatus::updateOrCreate(['device_id' => $device->id],
                [
                    'ac_status' => $this->terminalInfo()?->status,
                    'ignition' => $this->terminalInfo()?->ignition,
                    'charging' => $this->terminalInfo()?->charging,
                    'alarm_type' => $this->data()?->alarm_type,
                    'gps_tracking' => $this->terminalInfo()?->gpsTracking,
                    'relay_state' => $this->terminalInfo()?->relayState,
                    'voltage_level' => $this->data()?->voltageLevel,
                    'signal_level' => $this->data()?->signalLevel
                ]);
            });
        } catch(Exception $e){
            Log::error('Error storing Status Data: ' . $e->getMessage());
        };
    }

    /**
     * @return object
     */
    protected function data(): object
    {
        return (object)$this->data;
    }

    /**
     * @return object
     */
    protected function terminalInfo(): object
    {
        return (object)$this->data()->terminal_info;
    }
}
