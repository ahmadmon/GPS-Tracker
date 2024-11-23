<?php

use Carbon\Carbon;
use Morilog\Jalali\Jalalian;
use App\Facades\Acl;

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
    $color = $states[$stateNum];

    return $color;
}

function dayCount($startDate, $endDate)
{
    $startDate = Carbon::parse($startDate) ?? Carbon::now();
    $endDate = Carbon::parse($endDate);


    $diffinDays = $startDate->diffInDays($endDate);

    return $diffinDays;
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
        return priceFormat($number) . 'میلیارد ';
    } elseif ($number >= 1000000) {
        return priceFormat($number) . ' میلیون';
    } else {
        return priceFormat($number);
    }
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
}

function can(string $permission): bool
{
    return Acl::hasPermission($permission);
}

function cannot(string $permission): bool
{
    return !Acl::hasPermission($permission);
}

