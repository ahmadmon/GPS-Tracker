<?php

namespace App\Http\Interfaces;

use App\Models\Device;

interface DeviceInterface
{
    public function getCommand(string $commandKey, array $params = []): string;
    public function parseData(string $data, string $serial = null): array|string|null;
}
