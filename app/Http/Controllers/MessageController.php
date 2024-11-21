<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;

class MessageController extends Controller {

  public function store(Request $request) {
    $request->validate([
      "subject" => 'required|string|max:150',
      "message" => 'required|string',
      'g-recaptcha-check' => [
        'required',
        function (string $attribute, mixed $value, $fail) {
          $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config("services.recaptcha.secret_key"),
            'response' => $value,
            'remoteip' => \request()->ip(),
          ]);
          if (!$response->json('success')) {
            $fail('reCAPTCHA validation failed: ' . implode(', ', $response->json('error-codes')));
          }
        },
      ],
    ]);

    if (!auth()->check()) {
      $request->validate([
        "email" => 'required|email',
        "name" => 'required|string|max:40',
      ]);

      TelegramService::sendMessage(
        "📜📜📜 \n subject: " . request("subject") .
          "\n message: " . request("message") .
          "\n name: " . request("name") .
          "\n email: " . request("email"),
        request("email")
      );
    } else {

      TelegramService::sendMessage(
        "📜📜📜 \n subject: " . request("subject") .
          "\n message: " . request("message") .
          "\n user: " . auth()->user()->fullname(),
        auth()->user()->email
      );
    }

    $request->session()->flash('message-sent', true);

    return redirect()->route('contact_us');
  }

  public function redirect(Request $request, string $email) {

    if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return redirect("mailto:" . $email . "?Subject=Response From " . config("app.name"));
    }
    return abort(400, 'Invalid email');
  }
}
