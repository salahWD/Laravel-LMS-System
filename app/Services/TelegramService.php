<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService {

  public static function sendMessage(string $message, string $email = null): bool {
    $channelId = config('services.telegram.channel_id');
    $botToken = config('services.telegram.bot_token');

    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

    $data = [
      'chat_id' => $channelId,
      'text' => $message,
      'parse_mode' => 'HTML', // Optional, allows for bold, italic, etc.
    ];

    // Add inline keyboard if email is provided
    if ($email) {
      $data['reply_markup'] = json_encode([
        'inline_keyboard' => [
          [
            [
              'text' => "Reply to $email",
              'url' => route('redirect_to_email', $email)
            ]
          ]
        ]
      ], JSON_UNESCAPED_SLASHES);
    }

    $response = Http::post($url, $data);

    if (!$response->successful()) {
      // Log the response for debugging
      \Log::error('Telegram API Error', [
        'response' => $response->body(),
        'status' => $response->status(),
        'data' => $data,
      ]);
    }

    return $response->successful();
  }
}
