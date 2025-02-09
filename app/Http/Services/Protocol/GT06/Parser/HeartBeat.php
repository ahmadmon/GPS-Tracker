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
        return $this->values[6];
    }

    /**
     * @return string
     */
    protected function terminalByte(): string
    {
        return ;
    }

    /**
     * @return array
     */
    protected function terminalInfo(): array
    {
        return [
            'status' => boolval($this->statusInfo() & 0x01),
            'ignition' => boolval($this->statusInfo() & 0x02),
            'charging' => $this->charging(),
            'alarmType' => $this->alarmType(),
            'gpsTracking' => $this->gpsTracking(),
            'relayState' => $this->relayState(),
        ];
    }

    /**
     * @return string
     */
    protected function response(): string
    {
        return hex2bin("{$this->values[1]}05{$this->values[3]}0001D9DC0D0A");
    }
}
