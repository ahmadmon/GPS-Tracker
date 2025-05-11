<?php

namespace App\Helpers;

use App\Models\Config;
use Illuminate\Support\Facades\Http;

class Hadis
{

    public function __invoke(): void
    {
        $response = Http::get('https://api.keybit.ir/hadis')->json();


        if (!empty($response['result'])) {
            Config::updateOrCreate(['key' => 'daily-hadis'], [
                'value' => json_encode($response['result'])
            ]);
        }
    }

}
