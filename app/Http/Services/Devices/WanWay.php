<?php

namespace App\Http\Services\Devices;

use App\Http\Interfaces\DeviceInterface;
use Exception;
use Illuminate\Support\Facades\Config;

class WanWay implements DeviceInterface
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


    public function parseData(string $data, string $serial = null): array|string|null
    {
        $data = '78781f122c0610060408cc03d40c1c05811f3024146c01b02315860114fe121224540d0a';

        // Get Last Packet Data
        $packet = (strlen($data) > 72) ? getLastPacket($data) : $data;


        $startBit = substr($packet, 0, 4);
        $packetLength = hexdec(substr($packet, 4, 2));
        $protocolNumber = substr($packet, 6, 2);

        //if is Login Packet data then send a Response to device
        if ($protocolNumber == '01') {
            return hex2bin('787805010001d9dc0d0a');
        }
        //if is not Location Packet data then return null
        if ($protocolNumber != '12') {
            return null;
        }

        // check GPS status
        $courseStatus = hexdec(substr($packet, 20, 2)) & 0x80;
        if (!$courseStatus) {
            return null;
        }

        // Parse Date and Time
        $dateTime = [
            'year' => 2000 + hexdec(substr($packet, 8, 2)),
            'month' => hexdec(substr($packet, 10, 2)),
            'day' => hexdec(substr($packet, 12, 2)),
            'hour' => hexdec(substr($packet, 14, 2)),
            'minute' => hexdec(substr($packet, 16, 2)),
            'second' => hexdec(substr($packet, 18, 2))
        ];

        //parsing Points
        $lat = hexdec(substr($packet, 20, 8)) / 30000;
        $lng = hexdec(substr($packet, 28, 8)) / 30000;
        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            return null;
        }


        return [
            'device_id' => $serial,
            'date' => "{$dateTime['year']}-{$dateTime['month']}-{$dateTime['day']}",
            'time' => "{$dateTime['hour']}:{$dateTime['minute']}:{$dateTime['second']}",
            'gps_quantity' => hexdec(substr($packet, 10, 1)),
            'lac' => hexdec(substr($packet, 25, 2)),
            'cell_id' => hexdec(substr($packet, 27, 3)),
            'lat' => $lat,
            'long' => $lng,
            'speed' => hexdec(substr($packet, 36, 2))
        ];
    }
}
