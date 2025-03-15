<?php

namespace App\Http\Services\Server;

use App\Http\Services\Protocol\ParserAbstract;
use App\Http\Services\Protocol\ProtocolAbstract;
use App\Http\Services\Protocol\Resource\ResourceAbstract;
use App\Jobs\LogSuccessJob;
use App\Jobs\StoreGpsDataJob;
use App\Jobs\StoreTrackerInfoJob;
use Illuminate\Support\Arr;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Workerman\Connection\TcpConnection;
use Workerman\Timer;
use Workerman\Worker;
use App\Http\Services\Protocol\ProtocolFactory;
use Carbon\Carbon;

class MultiProtocolServer
{
    /**
     * @var array $servers
     */
    protected array $servers = [];

    /**
     * @var array $protocols
     */
    protected array $protocols = [];

    /**
     * @var array $loggers
     */
    protected array $loggers = [];


    public function __construct()
    {

        // Automatically load protocol managers using ProtocolFactory
        $this->protocols = ProtocolFactory::list();
    }

    /**
     * Add a server instance for each protocol and port.
     * @return void
     */
    public function initializeServers(): void
    {
        foreach ($this->protocols as $protocolCode => $protocolClass) {
            $protocolManager = ProtocolFactory::get($protocolCode);
            $this->addServer($protocolManager);
        }
    }

    /**
     * Add a server for the given protocol manager.
     *
     * @param ProtocolAbstract $protocolManager
     */
    public function addServer(ProtocolAbstract $protocolManager): void
    {
        try {
            // Set Logger for This Protocol
            $port = $protocolManager->port();
            $protocolName = strtolower($protocolManager->code());

            $this->loggers[$protocolName] = new Logger($protocolName);
            $logFile = storage_path("logs/{$protocolName}.log");
            $this->loggers[$protocolName]->pushHandler(new StreamHandler($logFile, Logger::DEBUG));

            // Set Server Info
            $serverIP = config('server-info.server');
            $worker = new Worker("tcp://{$serverIP}:{$port}");


            $worker->onMessage = function (TcpConnection $connection, $message) use ($protocolManager, $protocolName) {
                echo "Message Received at: " . jalaliDate(now(), format: "Y/m/d H:i:s") . "\n";
                $this->loggers[$protocolName]->info("Message Received", ['message' => $this->readBuffer($message)]);
                $this->handleMessage($protocolManager, $connection, $this->readBuffer($message));
            };

            $worker->onConnect = function (TcpConnection $connection) use ($protocolManager, $protocolName) {
                echo "New connection on protocol {$protocolManager->name()}\n";
                $this->loggers[$protocolName]->info("New Connection Established");
            };

            $worker->onClose = function (TcpConnection $connection) use ($protocolManager, $protocolName) {
                echo "Connection closed on protocol {$protocolManager->name()}\n";
                $this->loggers[$protocolName]->info("Connection Closed");

            };

            $worker->onError = function ($connection, $code, $msg) use ($protocolManager, $protocolName) {
                echo "Error on protocol {$protocolManager->name()} : $msg - (Code: $code)";
                $this->loggers[$protocolName]->error("Error: $msg (Code: $code)");

            };

            $this->servers[] = $worker;

            echo "Server started for protocol {$protocolManager->name()} on port {$port}\n";
            $this->loggers[$protocolName]->info("Server started on port {$port}");


        } catch (\Exception $e) {
            echo "Error running servers: " . $e->getMessage();
            return;
        }
    }

    /**
     * Handle incoming messages and delegate to protocol-specific parsers.
     *
     * @param ProtocolAbstract $protocolManager
     * @param TcpConnection $connection
     * @param string $buffer
     * @return void
     */
    protected function handleMessage(ProtocolAbstract $protocolManager, TcpConnection $connection, string $buffer): void
    {
        try {
            $protocolName = strtolower($protocolManager->code());
            $this->loggers[$protocolName]->info("Packet received", ['packet' => $buffer]);

            $resources = $protocolManager->resources($buffer, $connection);
            if (empty($resources)) return;

            foreach ($resources as $resource) {
                if ($resource->format() === 'location') {
                    $this->processLocationData($protocolName, $this->locationData($resource));
                }
                if ($resource->format() === 'heartBeat') {
                    $this->processHeartbeatData($protocolName, $this->heartBeatData($resource));
                }
            }

            $lastResource = end($resources);
            if ($lastResource !== null && !empty($lastResource->response())) {
                $response = $lastResource->response();
                $connection->send($response); // Send protocol-specific response from last resource

                echo "Response sent to Client: {$this->readBuffer($response)}";
                $this->loggers[$protocolName]->info("Response sent to Client", [
                    'serial' => $lastResource->serial(),
                    'format' => $lastResource->format(),
                    'message' => $this->readBuffer($response)
                ]);
            }

        } catch (\Exception $e) {
            $this->loggers[$protocolName]->error("Error processing message: " . $e->getMessage());
            return;
        }
    }

    /**
     * @param string $protocolName
     * @param array $locationData
     * @return void
     */
    protected function processLocationData(string $protocolName, array $locationData): void
    {
        try {
            $this->loggers[$protocolName]->info("Location data dispatched to queue", [
                'queue' => 'location',
                'data' => json_encode($locationData),
            ]);

            StoreGpsDataJob::dispatch($locationData)
                ->onQueue('location')
                ->chain([
                    new LogSuccessJob($protocolName, [
                        'queue' => 'location',
                        'serial' => $locationData['device_id']
                    ])
                ]);

        } catch (\Exception $e) {
            $this->loggers[$protocolName]->error("Failed to dispatch GPS data to queue", [
                'serial' => $locationData['device_id'],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * @param string $protocolName
     * @param array $heartbeatData
     * @return void
     */
    protected function processHeartbeatData(string $protocolName, array $heartbeatData): void
    {
        try {
            $this->loggers[$protocolName]->info("HeartBeat data dispatched to queue", [
                'queue' => 'heartbeat',
                'data' => json_encode($heartbeatData)
            ]);


            StoreTrackerInfoJob::dispatch($heartbeatData)
                ->onQueue('heartbeat')
                ->chain([
                    new LogSuccessJob($protocolName, [
                        'queue' => 'location',
                        'serial' => $heartbeatData['device_id']
                    ])
                ]);

        } catch (\Exception $e) {
            $this->loggers[$protocolName]->error("Failed to dispatch GPS data to queue", [
                'serial' => $heartbeatData['device_id'],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Prepare data for storing GPS information.
     *
     * @param ResourceAbstract $resource
     * @return array
     */
    protected function locationData(ResourceAbstract $resource): array
    {
        return [
            'device_id' => $resource->serial(),
            'lat' => $resource->latitude(),
            'long' => $resource->longitude(),
            'speed' => $resource->speed(),
            'signal' => $resource->signal(),
            'direction' => $resource->direction(),
            'datetime' => $resource->datetime(),
            'received_at' => Carbon::now()
        ];
    }

    /**
     * Prepare data for storing Tracker information.
     *
     * @param ResourceAbstract $resource
     * @return array
     */
    protected function heartBeatData(ResourceAbstract $resource): array
    {
        return [
            'device_id' => $resource->serial(),
            'terminal_info' => Arr::except($resource->terminalInfo(), ['alarmType']),
            'alarm_type' => $resource->terminalInfo()['alarmType'],
            'voltageLevel' => $resource->voltageLevel(),
            'signalLevel' => $resource->signalLevel(),
        ];
    }

    /**
     * Run all servers.
     */
    public function run(): void
    {

        global $argv;
        if (!isset($argv[1])) {
            $argv[1] = 'start';
        }


        Worker::runAll();
    }


    /**
     * @param string $buffer
     * @return string
     */
    protected function readBuffer(string $buffer): string
    {
        return bin2hex($buffer);
    }
}
