<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use React\EventLoop\Factory;
use React\Socket\Server;

class GpsServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps-server:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the TCP server to receive GPS data from devices';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $loop = Factory::create();

        $socket = new Server($loop);
        $socket->listen(8080);


        $socket->on('connection', function ($connection) {
            $this->info('New connection established');

            $connection->on('data', function ($data) use ($connection) {
                $this->info('Data received: ' . $data);

//                $parsedData = $this->parseGpsDate($data);

//                dd($parsedData);

                //add to Database
                //
            });
            $connection->write("Data received successfully");

        });

        $this->info("TCP server running on [http://127.0.0.1:8080]");
        $loop->run();
    }


    private function parseGpsDate($data): array
    {
        $parts = explode(',', $data);

        return [
            'imei' => $parts[0],
            'lat' => $parts[1],
            'lng' => $parts[2],
            'speed' => $parts[3],
            'received_at' => now(),
        ];
    }
}
