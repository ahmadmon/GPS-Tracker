<?php

namespace App\Http\Services\Devices;

use App\Http\Interfaces\DeviceInterface;
use App\Traits\ParserHelper;
use Exception;
use Illuminate\Support\Facades\Config;

class ConCox implements DeviceInterface
{
    use ParserHelper;

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
        $data = bin2hex($data);

        // Get Last Packet Data
        $packet = (strlen($data) > 84) ? getLastPacket($data) : $data;


        $startBit = substr($packet, 0, 4);
        $packetLength = hexdec(substr($packet, 4, 2));

        $protocolNumber = substr($packet, 6, 2);

        //if is Login Packet data then send a Response to device
        if ($protocolNumber == '01') {
            return hex2bin("{$startBit}05{$protocolNumber}0001D9DC0D0A");
        }
        //if is not Location Packet data then return null
        if (!in_array($protocolNumber, ['12', '16', '22'])) {
            return null;
        }

        // check GPS status
        $courseStatus = $this->courseStatus(substr($packet, 40, 4));
        if (!$courseStatus->has_signal) {
            return null;
        }

        //parsing Points
        $lat = $this->convertToRealCoordinates(substr($packet, 22, 8), $courseStatus->lat_dir === 'east');
        $lng = $this->convertToRealCoordinates(substr($packet, 30, 8), $courseStatus->lng_dir === 'south');


        return [
            'device_id' => $serial,
            'datetime' => $this->datetime(substr($packet, 8, 12)),
            'satellites' => $this->satellite(substr($packet, 20, 2)),
            'lac' => hexdec(substr($packet, 50, 4)),
            'cell_id' => hexdec(substr($packet, 54, 6)),
            'lat' => $lat,
            'long' => $lng,
            'direction' => $courseStatus->direction,
            'speed' => hexdec(substr($packet, 38, 2))
//            'mcc' => hexdec(substr($packet, 44, 4)),
//            'mnc' => hexdec(substr($packet, 48, 2)),
        ];
    }
}
