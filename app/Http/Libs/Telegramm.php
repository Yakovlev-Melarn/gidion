<?php

namespace App\Http\Libs;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class Telegramm
{
    public static function send($text, $authId)
    {
        $user = User::find($authId);
        Http::withHeaders([])
            ->timeout(3600)
            ->acceptJson()
            ->post("https://api.telegram.org/{$user->telegram}/sendMessage", [
                'chat_id' => "1139134460",
                'text' => $text
            ]);
    }
}
