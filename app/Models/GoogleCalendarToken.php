<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class GoogleCalendarToken extends Model {

  public $timestamps = false;

  protected $fillable = [
    "user_id",
    "token",
  ];

  public function user() {
    return $this->belongsTo(User::class);
  }
}
