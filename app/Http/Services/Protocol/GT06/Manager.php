<?php

namespace App\Http\Services\Protocol\GT06;

use App\Http\Services\Protocol\GT06\Parser\Auth as AuthParser;
use App\Http\Services\Protocol\GT06\Parser\HeartBeat as HeartBeatParser;
use App\Http\Services\Protocol\ProtocolAbstract;

class Manager extends ProtocolAbstract
{
    /**
     * @return string
     */
    public function code(): string
    {
        return 'gt060';
    }


    /**
     * @return string
     */
    public function name(): string
    {
        return 'GT06';
    }


    /**
     * @return array
     */
    protected function parsers(): array
    {
        return [
            AuthParser::class,
            HeartBeatParser::class
        ];
    }

    /**
     * @param string $message
     *
     * @return array
     */
    public function messages(string $message): array
    {
        return array_filter(array_map('trim', explode('0d0a', $message)));
    }
}
