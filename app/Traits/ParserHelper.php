<?php

namespace App\Traits;


trait ParserHelper
{
//    public function convertToRealCoordinates(string $input): float
//    {
//        $decimalValue = hexdec($input); // ConvertHexToDecimal
//
//        // Calculate_Dgree_Minutes
//        $coordinate = doubleval($decimalValue / 30000);
//        $degrees = floor($coordinate / 60);
//        $minutes = $coordinate - ($degrees * 60);
//
//        return round($degrees + ($minutes / 60), 6); // ConvertToDecimal
//    }

    public function decodeGt06Lat(string $lat): float
    {
        $decimalLat = hexdec($lat);
        return $decimalLat;
        $latitude = $decimalLat / 60.0 / 30000.0;

//        if (!($course & 0x0400)) {
//            $latitude = -$latitude;
//        }

        return round($latitude, 6);
    }

}

