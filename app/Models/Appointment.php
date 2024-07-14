<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AppointmentSetting;
use App\Models\BookedAppointment;

class Appointment extends Model {
  use HasFactory;

  public $fillable = [
    "admin_id",
    "title",
    "url",
    "description",
    "duration",
    "buffer_zone",
    "timezone",
    "link_google_calendar",
    "price",
    "color",
    "status",
  ];

  public function author() {
    return $this->belongsTo(User::class, "user_id");
  }

  public function settings() {
    return $this->hasMany(AppointmentSetting::class);
  }

  public function available_days() {
    return $this->settings()->whereIn("key", ["0", "1", "2", "3", "4", "5", "6"]);
  }

  public function excluded_days() {
    return $this->settings()->where("key", "excluded");
  }

  public function show_duration() {
    return $this->duration . "min";
  }

  public function booked() {
    return $this->hasMany(BookedAppointment::class);
  }

  public function scopeActive($query) {
    return $query->where('status', '=', 1);
  }
}
