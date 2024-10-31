<?php

namespace App\Enums;

enum DeviceBrand: string
{
    case SINOTRACK = 'sinotrack';
    case WANWAY = 'wanway';
    case CONCOX = 'concox';


    public function isSinotrack(): bool
    {
        return $this === self::SINOTRACK;
    }

    public function isWanWay(): bool
    {
        return $this === self::WANWAY;
    }

    public function isConcox(): bool
    {
        return $this === self::CONCOX;
    }
}
