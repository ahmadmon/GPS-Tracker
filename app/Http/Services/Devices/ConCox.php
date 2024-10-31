<?php

namespace App\Http\Services\Devices;

use App\Http\Interfaces\DeviceInterface;
use App\Models\Device;
use Exception;
use Illuminate\Support\Facades\Config;

class ConCox implements DeviceInterface
{

    protected string $password = '';
    protected string $ip;
    protected string $port;

    public function __construct(string $ip, string $port, $password = null)
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
        dd(str_replace(',,', ',', $template));
        return $template;
    }


    public function parseData(Device $device, string $data): array
    {
        $packet = substr(strrchr($data, '7878'), 0, strpos(strrchr($data, '7878'), '0d0a') + 4);

        $year = 2000 + hexdec(substr($packet, 8, 2));
        $month = hexdec(substr($packet, 10, 2));
        $day = hexdec(substr($packet, 12, 2));
        $hour = hexdec(substr($packet, 14, 2));
        $minute = hexdec(substr($packet, 16, 2));
        $second = hexdec(substr($packet, 18, 2));


        $latitudeHex = substr($packet, 20, 8);
        $longitudeHex = substr($packet, 28, 8);
        $speed = hexdec(substr($packet, 36, 2));

        $lat = hexdec($latitudeHex) / 1800000;
        $lng = hexdec($longitudeHex) / 1800000;


        return [
            'device_id' => $device->serial,
            'date' => "{$year}-{$month}-{$day}",
            'time' => "{$hour}:{$minute}:{$second}",
            'lat' => $lat,
            'long' => $lng,
            'speed' => $speed,
        ];
    }
}
