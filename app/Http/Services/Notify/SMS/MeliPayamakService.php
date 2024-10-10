<?php

namespace App\Http\Services\Notify\SMS;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;
use Melipayamak\Laravel\Facade as Melipayamak;
use Melipayamak\MelipayamakApi;

class MeliPayamakService
{
    private string $userName;
    private string $password;

    public function __construct()
    {
        $this->userName = Config::get('melipayamak.username');
        $this->password = Config::get('melipayamak.password');
    }

    public function send($from, $to, $text, $isFlash = true)
    {
        try {
            $sms = Melipayamak::sms();

            $response = $sms->send($to, $from, $text, $isFlash);

            return $this->parseResponse($response);

        } catch (\Exception $e) {
            return response()->json(['msg' => 'error sending SMS', 'error' => $e->getMessage()], 500);
        }
    }

    public function apiSend($from, $to, $text)
    {
        $api = new MelipayamakApi(username: $this->userName, password: $this->password);
        $sms = $api->sms();
        try {

            $response = $sms->send($to, $from, $text);

            return $this->parseResponse($response);

        } catch (\Exception $e) {
            return response()->json(['msg' => 'error sending SMS', 'error' => $e->getMessage()], 500);
        }
    }

//    public function getMessages($count, $from)
//    {
//        $api = new MelipayamakApi(username: $this->userName, password: $this->password);
//        $sms = $api->sms();
//
//        return $sms->getMessages(1, 0, $count, $from);
//    }

    private function parseResponse($response): JsonResponse
    {
        $errorCode = [
            0 => 'userName or password incorrect',
            2 => 'Credit is not enough.',
            3 => 'Limitation in daily sending',
            4 => 'Limitation in sending volume',
            5 => 'The sender number is not valid.',
            6 => 'The system is being updated.',
            7 => 'The text contains the filtered word.',
            9 => 'It is not possible to send from public lines through web service.',
            10 => 'The desired user is not active.',
            11 => 'Not Sent',
            12 => 'User documents are not complete.',
            14 => 'The text contains links.',
            15 => 'It is not possible to send to more than 1 mobile number without adding "Cancel 11"',
            35 => 'In REST, it means the existence of a number in the black list of communications.',
        ];
        $json = json_decode($response);

        $headers = ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'];

        if (in_array($json->Value, array_keys($errorCode))) {
            return response()->json(['msg' => 'error sending SMS', 'error' => $errorCode[$json->Value]],status: 500, headers: $headers);
        }
        return response()->json(['msg' => 'SMS sent successfully', 'response' => $json->Value], headers: $headers);
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
