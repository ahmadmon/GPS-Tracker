<?php

namespace App\Http\Services\Notify\SMS;

use App\Http\Interfaces\MessageInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;


class SmsService implements MessageInterface
{

    private $from = null;
    private $text;
    private $to;
    private $isFlash = true;

    public function fire(): JsonResponse
    {
        $this->from = Config::get('melipayamak.number');

        $meliPayamak = new MeliPayamakService();
        return $meliPayamak->send($this->from, $this->to, $this->text, $this->isFlash);
    }

    public function api(): JsonResponse
    {
        $this->from = Config::get('melipayamak.number');


        $meliPayamak = new MeliPayamakService();
        return $meliPayamak->apiSend($this->from, $this->to, $this->text);
    }

//    public function getMessages($count = 5)
//    {
//        $meliPayamak = new MeliPayamakService();
//        return $meliPayamak->getMessages($count, $this->from);
//    }

    public function getFrom()
    {
        return $this->from;
    }

    public function setFrom($from)
    {
        $this->from = $from;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getTo()
    {
        return $this->to;
    }

    public function setTo($to)
    {
        $this->to = $to;
    }

    public function getIsFlash()
    {
        return $this->to;
    }

    public function setIsFlash($flash)
    {
        $this->isFlash = $flash;
    }
}
