<?php

use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

function jalaliDate($date, $time = false, $format = "%d %B %Y", $ago = false)
{
    if ($ago) {
        return Jalalian::forge($date)->ago();
    } else {
        if($time){
            $format = "%d %B %Y H:i";
        }
        return Jalalian::forge($date)->format($format); // جمعه، 23 اسفند 97
    }
}

function convertJalaliToGregorian($date, $format = 'Y/m/d H:i:s'): string
{
    return Jalalian::fromFormat($format, $date)->toCarbon()->toDateTimeString();
}


function avatar($name = null, $family = null): string
{
    $stateNum = rand(0, 6);
    $states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
    $state = $states[$stateNum];
    if (!empty($name) && !empty($family)) {
        $initials = strtoupper((mb_substr($name, 0, 1)) . '‌' . (mb_substr($family, 0, 1)));
    } else {
        $initials = strtoupper((mb_substr($name, 0, 1)) . '‌' . (mb_substr($name, -1)));
    }
    return '<span class="avatar-initial rounded-circle pull-up fw-bold bg-label-' . $state . '">' . $initials . '</span>';
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


function sizeName(int $size)
{
    switch ($size) {
        case 0:
            $result = 'کوچک';
            break;
        case 1:
            $result = 'متوسط';
            break;
        case 2:
            $result = 'بزرگ';
            break;
        case 3:
            $result = 'خیلی بزرگ';
            break;
        default:
            $result = '';
    }
    return $result;
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

function cacheImage($path, $size = 'medium'): string
{
    $imgUrl = route('image.cache', ['path' => $path, 'size' => $size]);

    $pattern = '/\/[^\/]+$/';
    return preg_replace($pattern, '', $imgUrl);
}
