<?php

namespace App\Traits;


trait ParserHelper
{
    public function convertToRealCoordinates(string $input, bool $negative): float
    {
        $decimalValue = hexdec($input); // ConvertHexToDecimal

        // Calculate Degree Minutes
        $coordinate = doubleval($decimalValue / 30000);
        $degrees = floor($coordinate / 60);
        $minutes = $coordinate - ($degrees * 60);

        $value = round($degrees + ($minutes / 60), 6); // round the coordinates

        if ($negative) {
            $value *= -1;
        }

        return $value;
    }

    public function courseStatus($input): object
    {
        $courseStatus = str_pad(base_convert($input, 16, 2), 16, '0', STR_PAD_LEFT);
        $byte = substr($courseStatus, 0, 8);

        $value = [
            'has_signal' => (bool)$byte[3] == '1',
            'lng_dir' => $byte[4] == '1' ? 'east' : 'west',
            'lat_dir' => $byte[5] == '1' ? 'north' : 'south',
            'direction' => bindec(substr($courseStatus, 8, 8))
        ];

        return (object)$value;
    }

    public function datetime($input): ?string
    {
        $date = array_map(
            static fn($value) => str_pad(strval(hexdec($value)), 2, '0', STR_PAD_LEFT),
            str_split($input, 2)
        );

        return '20' . $date[0] . '-' . $date[1] . '-' . $date[2] . ' ' . $date[3] . ':' . $date[4] . ':' . $date[5];
    }

    public function satellite($input): int
    {
        $value = str_pad(base_convert($input, 16, 2), 8, '0', STR_PAD_LEFT);

        return (int)bindec(substr($value, 0, 2));
    }

}

