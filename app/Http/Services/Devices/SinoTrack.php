<?php

namespace App\Http\Services\Devices;

use App\Http\Interfaces\DeviceInterface;
use Exception;

class SinoTrack implements DeviceInterface
{

    protected string $password = '0000';
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
        // 0 => Server Setting
        // 1 => APN Setting
        // 2 => Upload Time
        // 3 => Change device password
        // 4 => set Admin Number
        // 5 => Hard Reset Factory
        // 5 => Other Commands
        $commands = [
            '0' => "804{$this->password} {$this->ip} {$this->port}",
            '1' => "803{$this->password} {apn}",
            '2' => "805{$this->password} {interval}",
            '3' => "777{password}{$this->password}",
            '4' => "{phone}{$this->password} 1",
            '5' => "RESET",
            '6' => "{other}"
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

    /**
     * @throws Exception
     */
    public function parseData($data, string $serial = null): array|null
    {
        // REMOVE '*' AND '#'
        $data = trim($data, "*#");

        $parts = explode(',', $data);

        if (count($parts) < 14) {
            throw new Exception("Invalid data format");
        }


        $parsedData = [
            'device_id' => $parts[1],         // (IMEI)
            'version' => $parts[2],           // Version or type of message
            'time' => $parts[3],              //report time in hhmmss format (hours, minutes and seconds).
            'validity' => $parts[4],          // positioning status (A means "valid" and V means "invalid").
            'lat' => $parts[5],
            'lat_dir' => $parts[6],           // latitude direction (N/S)
            'long' => $parts[7],
            'long_dir' => $parts[8],          // longitude direction (E/W)
            'speed' => $parts[9],             // Speed in km/h
            'direction' => $parts[10],        // Direction of movement in degrees.
            'date' => $parts[11],
            'status' => $parts[12],           //device status information (such as battery status, engine status, etc.).
            'lac' => $parts[13],              // LAC information.
            'cell_id' => $parts[14],          // Cell ID information
            'signal_strength' => $parts[15],  // signal strength information and other parameters.
        ];


        $parsedData['time'] = substr($parsedData['time'], 0, 2) . ':' . substr($parsedData['time'], 2, 2) . ':' . substr($parsedData['time'], 4, 2);
        $parsedData['date'] = '20' . substr($parsedData['date'], 4, 2) . '-' . substr($parsedData['date'], 2, 2) . '-' . substr($parsedData['date'], 0, 2);

        // Covert the Latitude and Longitude to Standard Format
        $parsedData['lat'] = $this->convertToDecimal($parsedData['lat'], $parsedData['lat_dir']);
        $parsedData['long'] = $this->convertToDecimal($parsedData['long'], $parsedData['long_dir']);

        return $parsedData['validity'] === 'A' ? $parsedData : null;
    }

    private function convertToDecimal($coordinate, $direction): float|int
    {
        // separate degrees and minutes
        $degrees = floor($coordinate / 100);
        $minutes = $coordinate - ($degrees * 100);
        $decimal = $degrees + ($minutes / 60);

        // if direction is South or West -> convert to negative.
        if ($direction == 'S' || $direction == 'W') {
            $decimal = -$decimal;
        }

        return $decimal;
    }
}
