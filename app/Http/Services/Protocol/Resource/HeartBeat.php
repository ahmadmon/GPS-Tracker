<?php

namespace App\Http\Services\Protocol\Resource;


class HeartBeat extends ResourceAbstract
{
    /**
     * @return array
     */
    protected function attributesAvailable(): array
    {
        return ['message', 'serial', 'data', 'terminalInfo', 'voltageLevel', 'signalLevel', 'response'];
    }

    /**
     * @return string
     */
    public function format(): string
    {
        return 'heartBeat';
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return boolval($this->serial());
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return $this->attribute(__FUNCTION__);
    }

    /**
     * @return array
     */
    public function terminalInfo(): array
    {
        return $this->attribute(__FUNCTION__);
    }

    /**
     * @return int
     */
    public function voltageLevel(): int
    {
        return $this->attribute(__FUNCTION__);
    }

    /**
     * @return int
     */
    public function signalLevel(): int
    {
        return $this->attribute(__FUNCTION__);
    }

    /**
     * @return string
     */
    public function serial(): string
    {
        return $this->attribute(__FUNCTION__);
    }

    /**
     * @return string
     */
    public function response(): string
    {
        return $this->attribute(__FUNCTION__);
    }
}
