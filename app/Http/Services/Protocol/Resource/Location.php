<?php

namespace App\Http\Services\Protocol\Resource;

use App\Http\Services\Protocol\Resource\ResourceAbstract;

class Location extends ResourceAbstract
{


    /**
     * @return array
     */
    protected function attributesAvailable(): array
    {
        return [
            'message', 'serial', 'type', 'latitude', 'longitude', 'speed',
            'signal', 'direction', 'datetime', 'data', 'response',
        ];
    }
    /**
     * @return string
     */
    public function format(): string
    {
        return 'location';
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->latitude() && $this->longitude() && $this->serial() && $this->datetime();
    }

    /**
     * @return string
     */
    public function message(): string
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
     * @return ?string
     */
    public function type(): ?string
    {
        return $this->attribute(__FUNCTION__);
    }

    /**
     * @return ?float
     */
    public function latitude(): ?float
    {
        return $this->attribute(__FUNCTION__);
    }

    /**
     * @return ?float
     */
    public function longitude(): ?float
    {
        return $this->attribute(__FUNCTION__);
    }

    /**
     * @return ?float
     */
    public function speed(): ?float
    {
        return $this->attribute(__FUNCTION__);
    }

    /**
     * @return ?int
     */
    public function signal(): ?int
    {
        return $this->attribute(__FUNCTION__);
    }

    /**
     * @return ?int
     */
    public function direction(): ?int
    {
        return $this->attribute(__FUNCTION__);
    }

    /**
     * @return ?string
     */
    public function datetime(): ?string
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
