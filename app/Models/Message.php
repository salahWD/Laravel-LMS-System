<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model {
  use HasFactory;

  protected $fillable = [
    "user_id",
    "name",
    "email",
    "subject",
    "is_website",
    "message",
  ];

  public function user() {
    return $this->belongsTo(User::class);
  }
}
