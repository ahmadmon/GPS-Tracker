<?php

namespace App\Console\Commands;

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
        // REMOVE '*' AND '#'
        $data = trim($data, "*#");

        $parts = explode(',', $data);

        if (count($parts) < 14) {
            throw new Exception("Invalid data format");
        }


        $parsedData = [
            'device_id' => $parts[1],         // (IMEI)
            'version' => $parts[2],           // Version or type of message
            'time' => $parts[3],              //report time in hhmmss format (hours, minutes and seconds).
            'validity' => $parts[4],          // positioning status (A means "valid" and V means "invalid").
            'lat' => $parts[5],
            'lat_dir' => $parts[6],           // latitude direction (N/S)
            'long' => $parts[7],
            'long_dir' => $parts[8],          // longitude direction (E/W)
            'speed' => $parts[9],             // Speed in km/h
            'direction' => $parts[10],        // Direction of movement in degrees.
            'date' => $parts[11],
            'status' => $parts[12],           //device status information (such as battery status, engine status, etc.).
            'lac' => $parts[13],              // LAC information.
            'cell_id' => $parts[14],          // Cell ID information
            'signal_strength' => $parts[15],  // signal strength information and other parameters.
        ];


        $parsedData['time'] = substr($parsedData['time'], 0, 2) . ':' . substr($parsedData['time'], 2, 2) . ':' . substr($parsedData['time'], 4, 2);
        $parsedData['date'] = '20' . substr($parsedData['date'], 4, 2) . '-' . substr($parsedData['date'], 2, 2) . '-' . substr($parsedData['date'], 0, 2);

        // Covert the Latitude and Longitude to Standard Format
        $parsedData['lat'] = $this->convertToDecimal($parsedData['lat'], $parsedData['lat_dir']);
        $parsedData['long'] = $this->convertToDecimal($parsedData['long'], $parsedData['long_dir']);

        return [
            'expectsResponse' => true,
            'response' => 'ACK',
            'data' => $parsedData
        ];
    }

    private function convertToDecimal($coordinate, $direction): float|int
    {
        // separate degrees and minutes
        $degrees = floor($coordinate / 100);
        $minutes = $coordinate - ($degrees * 100);
        $decimal = $degrees + ($minutes / 60);

        // if direction is South or West -> convert to negative.
        if ($direction == 'S' || $direction == 'W') {
            $decimal = -$decimal;
        }

        return $decimal;
    }
}
