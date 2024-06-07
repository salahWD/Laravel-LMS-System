<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Mail;
use App\Mail\MessageMail;
use App\Mail\MessageReply;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;

class MessageController extends Controller {

  public function response(Request $request, Message $message) {
    return view("dashboard.message", compact("message"));
  }

  public function store(Request $request) {
    $request->validate([
      "subject" => 'required|string|max:150',
      "message" => 'required|string',
      'g-recaptcha-check' => [
        'required',
        function (string $attribute, mixed $value, $fail) {
          $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config("services.recaptcha.secret_key"),
            'response' => null,
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

      // $message = Message::create([
      //   "name" => request("name"),
      //   "email" => request("email"),
      //   "subject" => request("subject"),
      //   "message" => request("message"),
      // ]);

      // $mail = Mail::to(config("app.admin_email"))->send(new MessageMail(
      // // $mail = Mail::to("salahb170@gmail.com")->send(new MessageMail(
      //   null,
      //   request("subject"),
      //   request("message"),
      //   request("name"),
      // ));
    } else {

      // $message = Message::create([
      //   "user_id" => auth()->user()->id,
      //   "subject" => request("subject"),
      //   "message" => request("message"),
      // ]);

      // $mail = Mail::to(config("app.admin_email"))->send(new MessageMail(
      //   auth()->user(),
      //   request("subject"),
      //   request("message"),
      // ));
    }

    $request->session()->flash('message-sent', true);

    return redirect()->route('contact_us');
  }


  public function send(Request $request, Message $message) {
    $request->validate([
      "reply" => 'required|string',
    ]);

    $reply = Message::create([
      "name" => auth()->user()->fullname(),
      "email" => auth()->user()->email,
      "subject" => "Reply On " . $message->subject,
      "is_website" => 1,
      "message" => request("reply"),
    ]);

    $email = null;
    if ($message->user_id) {
      $email = $message->user->email;
    } else {
      $email = $message->email;
    }

    $mail = Mail::to($email)->send(new MessageReply(
      $message->subject,
      request("reply"),
    ));

    $request->session()->flash('message-replied', true);

    return redirect()->route('messages_manage');
  }


  public function destroy(Request $request, Message $message) {
    if (auth()->user()->is_admin()) {

      $request->validate([
        "page" => "integer:min:0",
      ]);

      $per_page = 2; // message pagination
      $next_message = Message::orderBy("created_at", "DESC")->where("is_website", "0")->skip($per_page * request("page"))->with("user")->first();
      $res = $message->delete();

      if ($next_message != null) {
        $next_message = [
          "id" => $next_message->id,
          "user_id" => $next_message->user_id,
          "name" => $next_message->name,
          "email" => $next_message->email,
          "subject" => $next_message->subject,
          "date" => $next_message->created_at->format("Y-m-d ga"),
        ];
      }

      return ["result" => $res, "next_message" => $next_message];
    }
  }
}
