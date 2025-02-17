<?php

namespace App\Http\Services\Server;

use App\Http\Services\Protocol\ParserAbstract;
use App\Http\Services\Protocol\ProtocolAbstract;
use App\Http\Services\Protocol\Resource\ResourceAbstract;
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
     * @var Logger $logger
     */
    protected Logger $logger;

    /**
     * @var array $connections
     */
    protected array $connections = [];

    public function __construct()
    {
        // Initialize logger
        $this->logger = new Logger('MultiProtocolServer');
        $this->logger->pushHandler(new StreamHandler('logs/server.log', Logger::DEBUG));

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
            $port = $protocolManager->port();


            $worker = new Worker("tcp://31.214.251.139:{$port}");


            $worker->onMessage = function (TcpConnection $connection, $message) use ($protocolManager) {
                echo "Message Received at: " . jalaliDate(now(), format: "Y/m/d H:i:s") . "\n";
                $this->handleMessage($protocolManager, $connection, $this->readBuffer($message));
            };

            $worker->onConnect = function (TcpConnection $connection) use ($protocolManager) {
                echo "New connection on protocol {$protocolManager->name()}\n";
                $this->logger->info("New connection on protocol {$protocolManager->name()}");

                $this->connections[$connection->id] = [
                    'connection' => $connection,
                    'last_activity' => time(), // Track connection time
                ];
            };

            $worker->onClose = function (TcpConnection $connection) use ($protocolManager) {
                echo "Connection closed on protocol {$protocolManager->name()}\n";
                $this->logger->info("Connection closed on protocol {$protocolManager->name()}");

                //                $connectionKey = "{$connection->getRemoteIp()}:{$connection->getRemotePort()}";
                //                 ParserAbstract::removeSerial($connectionKey);
                //                 echo json_encode(ParserAbstract::getAllSerials());

                //                unset($this->connections[$connection->id]);
            };

            $worker->onError = function ($connection, $code, $msg) use ($protocolManager) {
                echo "Error on protocol {$protocolManager->name()} : $msg - (Code: $code)";
                $this->logger->error("Error on protocol {$protocolManager->name()} : $msg - (Code: $code)");

                //                unset($this->connections[$connection->id]);
            };

            $this->servers[] = $worker;

            echo "Server started for protocol {$protocolManager->name()} on port {$port}\n";
            $this->logger->info("Server started for protocol {$protocolManager->name()} on port {$port}");
        } catch (\Exception $e) {
            echo "Error running servers: " . $e->getMessage();
            $this->logger->error("Error running servers: " . $e->getMessage());
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
            $this->logger->info("Packet is: ", [$protocolManager->messages($buffer)]);

            $resources = $protocolManager->resources($buffer, $connection);
            if (empty($resources)) return;

            foreach ($resources as $resource) {
                if ($resource->format() === 'location') {
                    StoreGpsDataJob::dispatch($this->locationData($resource));
                }
                if ($resource->format() === 'heartBeat') {
                    dump($this->heartBeatData($resource));
                    StoreTrackerInfoJob::dispatch($this->heartBeatData($resource));
                }
            }

            $lastResource = end($resources);
            if ($lastResource !== null && !empty($lastResource->response())) {
                $connection->send($lastResource->response()); // Send protocol-specific response from last resource
            }

            // Update last activity time for the connection
            // $this->connections[$connection->id]['last_activity'] = time();

        } catch (\Exception $e) {
            $this->logger->error("Error processing message: " . $e->getMessage());
            return;
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
        // Start Timer to check inactive connection
        // check every 60 seconds
        //        Timer::add(60, fn() => $this->checkInactiveConnections());

        global $argv;
        if (!isset($argv[1])) {
            $argv[1] = 'start';
        }


        Worker::runAll();
    }

    //    /**
    //     * Check and close inactive connections.
    //     */
    //    protected function checkInactiveConnections(): void
    //    {
    //        $inactiveTimeout = 300; // Close connections inactive for 5 minutes (300 seconds)
    //        $currentTime = time();
    //
    //        foreach ($this->connections as $connectionId => $connectionData) {
    //            if ($currentTime - $connectionData['last_activity'] > $inactiveTimeout) {
    //                $connection = $connectionData['connection'];
    //                if ($connection) {
    //                    $this->logger->info("Closing inactive connection: $connectionId");
    //                    $connection->close();
    //                    unset($this->connections[$connectionId]);
    //                }
    //            }
    //        }
    //    }


    /**
     * @param string $buffer
     * @return string
     */
    protected function readBuffer(string $buffer): string
    {
        return bin2hex($buffer);
    }
}
