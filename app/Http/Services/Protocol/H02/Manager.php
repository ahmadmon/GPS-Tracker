<?php

namespace App\Http\Services\Protocol\H02;


use App\Http\Services\Protocol\H02\Parser\Location as LocationParser;
use App\Http\Services\Protocol\ProtocolAbstract;

class Manager extends ProtocolAbstract
{


    /**
     * @return string
     */
    public function code(): string
    {
        return 'h02';
    }


    /**
     * @return string
     */
    public function name(): string
    {
        return 'H02';
    }

    /**
     * @return int
     */
    public function port(): int
    {
        return config('protocols.protocols.H02');
    }


    /**
     * @return array
     */
    protected function parsers(): array
    {
        return [
            LocationParser::class,
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
