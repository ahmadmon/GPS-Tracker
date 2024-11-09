<?php

namespace App\Console\Commands;

use App\Http\Services\DeviceManager;
use App\Jobs\StoreGpsDataJob;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;

class GpsTcpServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:serve {action} {--d : run in daeman mode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a Workerman TCP server for GPS';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $action = $this->argument('action');
        $isDaemon = $this->option('d');

        switch ($action) {
            case 'start':
                $this->startServer($isDaemon);
                break;

            case 'stop':
                $this->stopServer();
                break;

            case 'restart':
                $this->restartServer($isDaemon);
                break;

            default:
                $this->error("Invalid action. Use start, stop, or restart.");
        }
    }


    protected function startServer($isDaemon): void
    {
        if ($isDaemon) {
            Worker::$daemonize = true;
        }

        $tcpWorker = new Worker('tcp://31.214.251.139:5024');
        $tcpWorker->count = 4;

        $tcpWorker->onConnect = function () {
            $this->info("Client connected\n");
        };

        $tcpWorker->onMessage = function (TcpConnection $connection, $data) {
            try {

                $parsedData = $this->parseData($data, $connection);

                if ($parsedData['expectsResponse']) {
                    $connection->send($parsedData['response']);
                    $this->info('Response Sent at: ' . now()->toDateTimeString());
                }

                $this->info('Received message at: ' . now()->toDateTimeString());

                if ($parsedData['data'] != null) {
                    StoreGpsDataJob::dispatch($parsedData['data']);
                    $this->info('Data Inserted to Database.');
                }

            } catch (Exception $e) {
                $this->error('Error parsing data: ' . $e->getMessage());
            }
        };

        $tcpWorker->onClose = function (TcpConnection $connection) {
            $uniqueKey = $connection->getRemoteIp() . ':' . $connection->getRemotePort();
            Cache::forget("device_{$uniqueKey}");
        };

        Worker::$pidFile = storage_path('logs/pidfile.pid');
        Worker::$logFile = storage_path('logs/logfile.log');

        Worker::runAll();
    }


    protected function stopServer(): void
    {
        Worker::stopAll();
        $this->info("Server stopped.\n");
    }

    protected function restartServer($isDaemon): void
    {
        $this->stopServer();
        $this->startServer($isDaemon);
    }

    /**
     * @throws Exception
     */
    private function parseData($data, $connection): array
    {

        $device = $this->detectDevice($data, $connection);

        $parsedData = null;
        if (isset($device)) {
            $deviceManager = new DeviceManager();
            $deviceBrand = $deviceManager->getDevice($device['brand']);
            $parsedData = $deviceBrand->parseData($data, $device['serial']);
        }

        return [
            'expectsResponse' => is_string($parsedData),
            'response' => is_string($parsedData) ? $parsedData : null,
            'data' => is_array($parsedData) ? $parsedData : null
        ];
    }

    private function detectDevice($data, $connection): array|null
    {
        $uniqueKey = $connection->getRemoteIp() . ':' . $connection->getRemotePort();

        if (str_starts_with($data, '*HQ')) {
            preg_match('/\*HQ,(\d{10,15}),/', $data, $matches);
            return [
                'brand' => 'sinotrack',
                'serial' => $matches[1] ?? null,
            ];
        } else {
            $data = bin2hex($data);
            if (str_starts_with($data, '7878')) {
                $protocolNumber = substr($data, 6, 2);
                if ($protocolNumber === '01') {  // Login Packet
                    $serial = substr($data, 9, 15);
                    $brand = (in_array(strlen($protocolNumber), [36, 44])) ? 'concox' : 'wanway';

                    Cache::put("device_{$uniqueKey}", ['brand' => $brand, 'serial' => $serial], 600);

                    return [
                        'brand' => $brand,
                        'serial' => $serial,
                    ];
                }

                if (in_array($protocolNumber, ['12', '16', '22'])) {  // Location or Alarm Packet
                    $cachedDevice = Cache::get("device_{$uniqueKey}");
                    if ($cachedDevice) {
                        return $cachedDevice;
                    }
                }
            }
        }

        return null;
    }

}
