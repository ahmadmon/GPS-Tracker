<?php

namespace App\Console\Commands;

use Exception;
use Workerman\Worker;
use App\Jobs\StoreGpsDataJob;
use Illuminate\Console\Command;
use App\Http\Services\DeviceManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\Process;
use Log;
use Workerman\Connection\TcpConnection;

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

        $SERVER = Config::get('server-info.server');
        $PORT = Config::get('server-info.port');

        $tcpWorker = new Worker("tcp://0.0.0.0:5022");
        $tcpWorker->count = 4;

        $tcpWorker->onConnect = function () {
            $this->info("Client connected\n");
        };

        $tcpWorker->onMessage = function (TcpConnection $connection, $data) {
            try {

                $parsedData = $this->parseData($data, $connection);

                if ($parsedData['expectsResponse']) {
                    $connection->send($parsedData['response']);
                    $this->info('Response Sent at: ' . jalaliDate(now(), format: 'Y-m-d H:i:s'));
                }

                $this->info('Received data at: ' . jalaliDate(now(), format: 'Y-m-d H:i:s'));

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
        try {
            $pidFile = storage_path('logs/pidfile.pid');

            // Check if PID file exists
            if (!file_exists($pidFile)) {
                $this->info("PID File not Found.\n");
            }

            // Read PID from file
            $pid = trim(file_get_contents($pidFile));

            if (empty($pid)) {
                $this->info("PID file is empty.\n");
            }

            // Create the sudo kill command
            $process = new Process(['sudo', '-S', 'kill', '-9', $pid]);
            $process->setInput("2SwL3uxlnrrcHX3\n"); // Pass sudo password

            $process->run();

            if ($process->isSuccessful()) {
                // Try to remove the PID file
                @unlink($pidFile);

                $this->info("Gps Server with PID {$pid} successfully terminated.\n");
            }


            $this->info("Failed to terminate Gps Server: ' . {$process->getErrorOutput()}\n");
        } catch (Exception $e) {
            Log::error('Error stopping server: ' . $e->getMessage());
            $this->info("error message: {$e->getMessage()}\n");
        }
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
        dump($uniqueKey);

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
