<?php

namespace App\Http\Services\Protocol\GT06\Parser;

use App\Http\Services\Protocol\ParserAbstract;

class HeartBeat extends ParserAbstract
{

    /**
     * @return array
     */
    public function resources(): array
    {
        $this->values = [];


        if ($this->messageIsValid() === false) {
            return [];
        }

        $this->addIfValid($this->resourceHeartbeat());

        return $this->resources;
    }


    /**
     * @return bool
     */
    public function messageIsValid(): bool
    {

        return ($this->serial() ?? false)
            && (bool)preg_match($this->messageIsValidRegExp(), $this->message, $this->values);
    }

    /**
     * @return string
     */
    protected function messageIsValidRegExp(): string
    {
        return '/^'
            . '(7878)'        // 1 - start
            . '([0-9a-f]{2})' // 2 - length
            . '(13|23)'       // 3 - protocol
            . '([0-9a-f]{8})' // 4 - status information
            . '/';
    }

    /**
     * @return string
     */
    protected function serial(): string
    {
        return self::getSerial($this->connectionKey());
    }

    /**
     * @return string
     */
    protected function statusInfo(): string
    {
        return $this->values[4];
    }

    /**
     * @return string
     */
    protected function terminalByte(): string
    {
        return hexdec(substr($this->statusInfo(), 0, 2));
    }

    /**
     * @return string
     */
    protected function alarmType(): string
    {
        $alarm = ($this->terminalByte() & 0x38) >> 3;

        return match ($alarm) {
            1 => 'shock', // shock
            2 => 'power cut',
            3 => 'low battery',
            4 => 'sos',
            default => 'normal'
        };
    }

    /**
     * @return array
     */
    protected function terminalInfo(): array
    {
        return [
            'status' => boolval($this->terminalByte() & 0x01),
            'ignition' => boolval($this->terminalByte() & 0x02),
            'charging' => boolval($this->terminalByte() & 0x04),
            'alarmType' => $this->alarmType(),
            'gpsTracking' => boolval($this->terminalByte() & 0x40),
            'relayState' => boolval($this->terminalByte() & 0x80),
        ];
    }

    /**
     * @return int
     */
    protected function voltageLevel(): int
    {
        $voltageLevel = hexdec(substr($this->statusInfo(), 2, 2));

        return match ($voltageLevel) {
            0 => 0, // no power (shutdown)
            1 => 1, // extremely low battery
            2 => 2, // very low battery
            3 => 3, // low battery (can be used normally),
            4 => 4, // medium
            5 => 5, // high
            6 => 6, // very high
            default => 7 // unknown
        };
    }

    /**
     * @return int
     */
    protected function signalLevel(): int
    {
        $signalLevel = hexdec(substr($this->statusInfo(), 4, 2));

        return match ($signalLevel) {
            0 => 0, // no signal
            1 => 1, // extremely weak signal
            2 => 2, // very weak signal
            3 => 3, // good signal,
            4 => 4, // strong signal
            default => 5 // unknown
        };
    }


    /**
     * @return string
     */
    protected function response(): string
    {
        return hex2bin("{$this->values[1]}05{$this->values[3]}0001E9F10D0A");
    }
}
