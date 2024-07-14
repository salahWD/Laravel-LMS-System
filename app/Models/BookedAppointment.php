<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;

class BookedAppointment extends Model {
  use HasFactory;

  protected $casts = [
    'appointment_date' => 'datetime',
  ];

  protected $fillable = [
    "booker_id",
    "appointment_id",
    "secret_key",
    "appointment_date",
    "meeting_link",
    "status", // 0 => canceled | 1 => active | 2 => pending
    "notes",
  ];

  public function appointment() {
    return $this->belongsTo(Appointment::class);
  }

  public function booker() {
    return $this->belongsTo(User::class);
  }

  public function day() {
    return $this->appointment_date->format("F d, Y");
  }

  public function time() {
    // 8:30 AM
    return $this->appointment_date->format("H:i A");
  }

  public function hour() {
    return $this->appointment_date->format("g");
  }

  public function format() {
    return $this->appointment_date->format("A");
  }

  public function minute() {
    return $this->appointment_date->format("i");
  }

  public function created_at_date() {
    return $this->appointment_date->format("F d, Y H:i A");
  }

  public function scopeUpcoming($query) {
    return $query->where("status", 1)->where("appointment_date", ">", now());
  }

  public function scopeCancelled($query) {
    return $query->where("status", 0);
  }

  public function scopePast($query) {
    return $query->where("appointment_date", '<', now());
  }

  public function scopeFor($query, $param) {
    return $query->where("booker_id", $param);
  }
}
