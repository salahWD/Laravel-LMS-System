<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService {

  public static function sendMessage(string $message): bool {
    $channelId = config('services.telegram.channel_id');
    $botToken = config('services.telegram.bot_token');

    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

    $response = Http::post($url, [
      'chat_id' => $channelId,
      'text' => $message,
    ]);

    return $response->successful();
  }
}
