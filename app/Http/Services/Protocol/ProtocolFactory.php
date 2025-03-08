<?php


namespace App\Http\Services\Protocol;


use App\Http\Services\Protocol\GT06\Manager as GT06Manager;
use App\Http\Services\Protocol\H02\Manager as H02Manger;
use http\Exception\UnexpectedValueException;

class ProtocolFactory
{

    /**
     * @return array
     */
    public static function list(): array
    {
        return [
            'gt06' => GT06Manager::class,
            'h02' => H02Manger::class
        ];
    }

    /**
     * Get Protocol Code and return an Object Protocol Class
     *
     * @param string $code
     * @return ProtocolAbstract
     */
    public static function get(string $code): ProtocolAbstract
    {
        return static::new(static::class($code));
    }


    /**
     * Create an Instance of Protocol Class From argument
     *
     * @param string|null $class
     * @return ProtocolAbstract
     */
    protected static function new(?string $class): ProtocolAbstract
    {
        if (is_null($class)) {
            throw new UnexpectedValueException('Protocol Not Found!');
        }

        return new $class;
    }

    /**
     * @param string $code
     * @return string|null
     */
    protected static function class(string $code): ?string
    {
        return static::list()[$code] ?? null;
    }
}
