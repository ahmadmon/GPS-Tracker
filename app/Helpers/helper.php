<?php

use App\Facades\Acl;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Morilog\Jalali\Jalalian;

function jalaliDate($date, $time = false, $format = "%d %B %Y", $ago = false)
{
    if ($ago) {
        return Jalalian::forge($date)->ago();
    } else {
        if ($time) {
            $format = "%d %B %Y H:i";
        }
        return Jalalian::forge($date)->format($format); // جمعه، 23 اسفند 97
    }
}

function convertJalaliToGregorian($date, $format = 'Y/m/d H:i:s'): string
{
    return Jalalian::fromFormat($format, $date)->toCarbon()->toDateTimeString();
}


function randomBadge()
{
    $stateNum = rand(0, 6);
    $states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
    $state = $states[$stateNum];
    $badge = "badge bg-label-$state";
    return $badge;
}

function randomColor()
{
    $stateNum = rand(0, 6);
    $states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
    return $states[$stateNum];
}

function dayCount($endDate, $startDate = null)
{
    $startDate = Carbon::parse($startDate) ?? Carbon::now();
    $endDate = Carbon::parse($endDate);


    return max(0, $startDate->diffInDays($endDate));
}

function priceFormat($price): string
{
    return number_format($price, 0, "/");
}

function formatNumber($number): string
{
    if ($number >= 1000000000) {
        $formattedNumber = $number / 1000000000;
        return (floor($formattedNumber) == $formattedNumber)
            ? floor($formattedNumber) . 'B'
            : number_format($formattedNumber, 2) . 'B';
    } elseif ($number >= 1000000) {
        $formattedNumber = $number / 1000000;
        return (floor($formattedNumber) == $formattedNumber)
            ? floor($formattedNumber) . 'M'
            : number_format($formattedNumber, 2) . 'M';
    } elseif ($number >= 1000) {
        $formattedNumber = $number / 1000;
        return (floor($formattedNumber) == $formattedNumber)
            ? floor($formattedNumber) . 'K'
            : number_format($formattedNumber, 2) . 'K';
    } else {
        return (string)$number;
    }
}

function persianPriceFormat($number): string
{
    if ($number >= 1000000000) {
        $value = $number / 1000000000;
        return formatWithoutRounding($value) . ' میلیارد تومان';
    } elseif ($number >= 1000000) {
        $value = $number / 1000000;
        return formatWithoutRounding($value) . ' میلیون تومان';
    } else {
        return number_format($number) . ' تومان';
    }
}

function formatWithoutRounding($value): string
{
    $strValue = (string)$value;

    if (!str_contains($strValue, '.')) {
        return number_format($value);
    }

    list($integerPart, $decimalPart) = explode('.', $strValue);

    $decimalPart = rtrim($decimalPart, '0');

    if (empty($decimalPart)) {
        return number_format($integerPart);
    }

    return number_format($integerPart) . '.' . $decimalPart;
}

function is_image($file)
{
    $allowedMimeTypes = ['image/jpeg', 'image/gif', 'image/png', 'image/jpg', 'image/bmp', 'image/svg+xml'];
    $contentType = mime_content_type($file);

    if (in_array($contentType, $allowedMimeTypes)) {
        return true;
    }
    return false;
}


function randomKey(int $number = 10)
{
    return now() . '-' . \Illuminate\Support\Str::random($number);
}

function uniqueRandomNumbersWithinRange($min, $max, $quantity)
{
    $numbers = range($min, $max);
    shuffle($numbers);
    return implode('', array_slice($numbers, 0, $quantity));
}

function shortHash(string $string, $type = 'hash'): string
{
    $str = '';
    if ($type == 'hash') {
        $str = base64_encode($string);
    } else if ($type == 'un-hash') {
        $str = base64_decode($string);
    }
    return $str;
}

function getLastPacket($data): string
{
    $lastPacket = '';
    $start = 0;
    while (($pos = strpos($data, '7878', $start)) !== false) {
        $end = strpos($data, '0d0a', $pos) + 4;
        $lastPacket = substr($data, $pos, $end - $pos);
        $start = $end;
    }
    return $lastPacket;
}

function maskPhoneNumber($phoneNumber): string
{
    if (strlen($phoneNumber) === 11) {
        return substr_replace($phoneNumber, "***", -7, 3);
    }
    return $phoneNumber;
}

function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2): float|int
{
    $earthRadius = 6371;

    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);

    $deltaLat = $lat2 - $lat1;
    $deltaLon = $lon2 - $lon1;

    $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
        cos($lat1) * cos($lat2) *
        sin($deltaLon / 2) * sin($deltaLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earthRadius * $c;
}

/**
 * @param string $cache
 * @return void
 */
function forgetCache(string $cache): void
{
    if (Cache::has($cache))
        Cache::forget($cache);
}

function can(string $permission): bool
{
    return Acl::hasPermission($permission);
}

function cannot(string $permission): bool
{
    return !Acl::hasPermission($permission);
}

