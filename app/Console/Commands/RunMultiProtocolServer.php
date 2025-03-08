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
    protected $signature = 'server:run
    	{action? : The command to run (start, stop, restart, reload, status, connections)}
    	{mode? : The mode to run in (-d for daemon mode, -g for graceful stop/reload)}';

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
        $action = $this->argument('action') ?? 'start';
        $mode = $this->argument('mode') ?? '';

        $this->info("Starting multi-protocol server...");

        $server = new MultiProtocolServer();
        $server->initializeServers();

        // Pass the command and mode to Workerman
        global $argv;
        $argv = array_merge(['artisan'], explode(' ', "server:run $action $mode"));

        $server->run();
    }
}
