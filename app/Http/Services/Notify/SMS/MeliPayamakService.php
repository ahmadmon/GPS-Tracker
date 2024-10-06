<?php

namespace App\Http\Services\Notify\SMS;

use Melipayamak\Laravel\Facade as Melipayamak;

class MeliPayamakService
{


    public function send($from, $to, $text, $isFlash = true)
    {
        try {
            $sms = Melipayamak::sms();

            $response = $sms->send($to, $from, $text, $isFlash);
            $json = json_decode($response);
            return $json->Value;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    // public function sendScheduleSMS($from, $to, $text, $isFlash = true, $scheduleDateTime, $period = null)
    // {
    //     try {

    //         $sms = Melipayamak::sms();


    //         $response = $sms->send($to, $from, $text, $isFlash, $scheduleDateTime, $period);
    //         $json = json_decode($response);
    //         return $json->Value;
    //     } catch (Exception $e) {
    //         echo $e->getMessage();
    //     }
    // }
}
