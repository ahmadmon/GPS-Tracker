<?php

namespace App\Console\Commands;

use App\Http\Services\Server\MultiProtocolServer;
use Illuminate\Console\Command;

class RunMultiProtocolServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the multi-protocol server';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle(): void
    {
        $this->info("Starting multi-protocol server...");

        $server = new MultiProtocolServer();
        $server->initializeServers();
        $server->run();
    }
}
