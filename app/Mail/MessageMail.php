<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MessageMail extends Mailable {
  use Queueable, SerializesModels;

  public $fullname;
  public $subject;
  public $text;

  public function __construct(User $user = null, $subject, $msg, $name = null) {
    if ($user) {
      $this->fullname = $user->fullname();
    } else {
      $this->fullname = $name;
    }
    $this->subject = $subject;
    $this->text = $msg;
  }

  // public function contact_form($user_email): Envelope {
  //   return new Envelope(
  //     from: $user_email,
  //     subject: config("app.name") . ' | New contact message',
  //   );
  // }

  public function envelope(): Envelope {
    return new Envelope(
      from: config("app.website_email"),
      subject: config("app.name") . ' | New contact message',
    );
  }

  public function content(): Content {

    $phrases = [
      __("good luck"),
      __("phrase tow"),
      __("phrase three"),
    ];

    $phrase = $phrases[array_rand($phrases)];

    return new Content(
      view: 'emails.default',
      with: [
        "fullname" => $this->fullname,
        "message" => $this->text,
        "subject" => $this->subject,
        "phrase" => $phrase,
      ]
    );
  }

  /**
   * Get the attachments for the message.
   *
   * @return array<int, \Illuminate\Mail\Mailables\Attachment>
   */
  public function attachments(): array {
    return [];
  }
}
