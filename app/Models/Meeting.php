<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MeetingSetting;

class Meeting extends Model {
  use HasFactory;

  public $fillable = [
    "user_id",
    "title",
    "url",
    "description",
    "duration",
    "buffer_zone",
    "price",
    "color",
    "status",
  ];

  public function settings() {
    return $this->hasMany(MeetingSetting::class);
  }

  public function available_days() {
    return $this->settings()->whereIn("key", ["0", "1", "2", "3", "4", "5", "6"]);
  }

  public function excluded_days() {
    return $this->settings()->where("key", "excluded");
  }
}
