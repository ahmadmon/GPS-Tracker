<?php

namespace App\Console\Commands;

use App\Http\Services\DeviceManager;
use App\Jobs\StoreGpsDataJob;
use Exception;
use Illuminate\Console\Command;
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

        $tcpWorker->onConnect = function ($connection) {
            $this->info("Client connected\n");
        };

        $tcpWorker->onMessage = function (TcpConnection $connection, $data) {
            try {

                $parsedData = $this->parseData($data);

                if ($parsedData['expectsResponse']) {
                    $connection->send($parsedData['response']);
                }

                $this->info('Received message. ' . now()->toDateTimeString());

                StoreGpsDataJob::dispatch($parsedData['data']);

            } catch (Exception $e) {
                $this->error('Error parsing data: ' . $e->getMessage());
            }
        };

        Worker::$pidFile = storage_path('logs/pidfile.pid');
        Worker::$logFile = storage_path('logs/logfile.log');

        Worker::runAll();
    }

    // تابع متوقف کردن سرور
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
    private function parseData($data): array
    {

        $brand = $this->detectDeviceBrand($data);

//        $deviceManager = new DeviceManager()


//        return [
//            'expectsResponse' => true,
//            'response' => 'ACK',
//            'data' => $parsedData
//        ];
    }

    private function detectDeviceBrand($data): string
    {
        if (str_starts_with($data, '7878')) {
            if (strlen($data) == 28) {
                return 'concox';
            } elseif (strlen($data) == 32) {
                return 'wanway';
            }
        } elseif (str_starts_with($data, '*HQ')) {
            return 'sinotrack';
        }

        return 'Unknown';
    }

//    private function convertToDecimal($coordinate, $direction): float|int
//    {
//        // separate degrees and minutes
//        $degrees = floor($coordinate / 100);
//        $minutes = $coordinate - ($degrees * 100);
//        $decimal = $degrees + ($minutes / 60);
//
//        // if direction is South or West -> convert to negative.
//        if ($direction == 'S' || $direction == 'W') {
//            $decimal = -$decimal;
//        }
//
//        return $decimal;
//    }
}
