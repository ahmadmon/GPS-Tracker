<?php

namespace App\Jobs;

use App\Http\Services\Notify\SMS\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class SendSms implements ShouldQueue
{
    use Queueable, Dispatchable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected string $phoneNumber, protected string $message)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(SmsService $smsService): void
    {
        $smsService->setTo($this->phoneNumber);
        $smsService->setText($this->message);
        $smsService->api();
    }
}
