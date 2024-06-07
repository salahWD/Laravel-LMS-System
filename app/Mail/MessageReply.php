<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MessageReply extends Mailable {
  use Queueable, SerializesModels;

  public function __construct(public $subject, public $text) {
    //
  }

  public function envelope(): Envelope {
    return new Envelope(
      from: config("app.admin_email"),
      subject: $this->subject,
    );
  }

  public function content(): Content {
    return new Content(
      view: 'emails.default-reply',
      with: [
        'text' => $this->text
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
