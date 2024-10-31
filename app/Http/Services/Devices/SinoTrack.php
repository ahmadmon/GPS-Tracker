<?php

namespace App\Http\Services\Devices;

use App\Http\Interfaces\DeviceInterface;
use Exception;

class SinoTrack implements DeviceInterface
{

    protected string $password = '0000';
    protected string $ip;
    protected string $port;

    public function __construct(string $ip,string $port,$password = null)
    {
        $this->ip = $ip;
        $this->port = $port;

        if (isset($password))
            $this->password = $password;
    }


    /**
     * @throws Exception
     */
    public function getCommand(string $commandKey, array $params = []): string
    {
        // 0 => Server Setting
        // 1 => APN Setting
        // 2 => Upload Time
        // 3 => Change device password
        // 4 => set Admin Number
        // 5 => Hard Reset Factory
        $commands = [
            '0' => "804{$this->password} {$this->ip} {$this->port}",
            '1' => "803{$this->password} {apn}",
            '2' => "805{$this->password} {interval}",
            '3' => "777{password}{$this->password}",
            '4' => "{phone}{$this->password} 1",
            '5' => "RESET",
        ];

        $commandTemplate = $commands[$commandKey] ?? null;

        if (!$commandTemplate) {
            throw new Exception("Command not found");
        }

        return $this->parseCommand($commandTemplate, $params);
    }

    protected function parseCommand($template, $parameters)
    {
        foreach ($parameters as $key => $value) {
            $template = str_replace("{{$key}}", $value, $template);
        }
        return $template;
    }
}
