<?php

namespace App\Http\Libs;

use Illuminate\Support\Facades\Http;

class Telegramm
{
    public static function send($text)
    {
        Http::withHeaders([])
            ->timeout(3600)
            ->acceptJson()
            ->post("https://api.telegram.org/bot1667207989:AAEph_dWGYM1oX8HEKDy0PPNVVAkU4ry0VE/sendMessage", [
                'chat_id' => "1139134460",
                'text' => $text
            ]);
    }
}
