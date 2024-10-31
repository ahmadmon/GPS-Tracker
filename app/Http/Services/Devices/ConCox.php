<?php

namespace App\Http\Services\Devices;

use App\Http\Interfaces\DeviceInterface;
use Exception;
use Illuminate\Support\Facades\Config;

class ConCox implements DeviceInterface
{

    protected string $password = '';
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
        $meliPayamakNumber = Config::get('melipayamak.number');

        // 0 => Server Setting
        // 1 => APN Setting
        // 2 => Upload Time
        // 3 => Change device password
        // 4 => set Admin Number
        // 5 => Hard Reset Factory
        $commands = [
            '0' => "SERVER,{$this->password},0,{$this->ip},{$this->port},0#",
            '1' => "APN,{$this->password},{apn}#",
            '2' => "TIMER,{$this->password},{interval},3600#",
            '3' => "PASSWORD,{$this->password},{password}",
            '4' => "SOS,{$this->password},A,{phone},{$meliPayamakNumber}#",
            '5' => "FACTORY,{$this->password},#",
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
