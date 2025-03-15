<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LogSuccessJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected string $protocolName, protected array $logData)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $logFile = storage_path("logs/{$this->protocolName}.log");
        $logger = new Logger($this->protocolName);
        $logger->pushHandler(new StreamHandler($logFile, Logger::DEBUG));

        $logger->info("Job processed successfully", $this->logData);
    }
}
