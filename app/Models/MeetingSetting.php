<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingSetting extends Model {
  use HasFactory;

  public $fillable = [
    "meeting_id",
    "value",
    "key",
  ];

  public $timestamps = false;
}
