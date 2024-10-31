<?php

namespace App\Http\Interfaces;

interface DeviceInterface
{
    public function getCommand(string $commandKey, array $params = []): string;
}
