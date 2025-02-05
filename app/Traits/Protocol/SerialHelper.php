<?php

namespace App\Traits\Protocol;

trait SerialHelper {

    /**
     * @var array $serial
     */
    protected static array $serials = [];

    /**
     *
     * @return array
     */
    protected function data(): array
    {
        return ['serial' => self::$serials[$this->connectionId] ?? ''];
    }

      /**
     *
     * @param string $connectionId
     * @param string $serial
     */
    public static function setSerial(string $connectionId, string $serial): void
    {
        self::$serials[$connectionId] = $serial;
    }

    /**
     *
     * @param string $connectionId
     * @return string
     */
    public static function getSerial(string $connectionId): string
    {
        return self::$serials[$connectionId] ?? '';
    }


    /**
     *
     * @param string $connectionId
     */
    public static function removeSerial(string $connectionId): void
    {
        unset(self::$serials[$connectionId]);
    }
}
//
