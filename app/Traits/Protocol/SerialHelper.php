<?php

namespace App\Traits\Protocol;

trait SerialHelper
{

    /**
     * @var array $serial
     */
    protected static array $serials = [];


    /**
     *
     * @param string $connectionKey
     * @param string $serial
     */
    public static function setSerial(string $connectionKey, string $serial): void
    {
        self::$serials[$connectionKey] = $serial;
    }

    /**
     *
     * @param string $connectionKey
     * @return string
     */
    public static function getSerial(string $connectionKey): string
    {
        return self::$serials[$connectionKey] ?? '';
    }


    /**
     *
     * @param string $connectionKey
     */
    public static function removeSerial(string $connectionKey): void
    {
        unset(self::$serials[$connectionKey]);
    }
}
//
