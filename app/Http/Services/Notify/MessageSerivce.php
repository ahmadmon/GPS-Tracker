<?php

namespace App\Http\Services\Notify;

use App\Http\Interfaces\MessageInterface;

class MessageSerivce
{
    private $message;

    public function __construct(MessageInterface $message)
    {
        $this->message = $message;
    }

    public function send()
    {
        return $this->message->fire();
    }
}
