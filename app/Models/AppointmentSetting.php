<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Appointment;

class AppointmentSetting extends Model {
  use HasFactory;

  // protected $casts = [
  //   'day' => 'date',
  // ];

  public $fillable = [
    "appointment_id",
    "day",
    "from_date",
    "to_date",
    "key",
  ];

  public function appointment() {
    return $this->belongsTo(Appointment::class);
  }

  public function fromDateCarbon() {
    return str_contains($this->from_date, "T") ? Carbon::createFromFormat('H:i:sP', substr($this->from_date, 2)) : Carbon::createFromFormat('H:i:sP', $this->from_date);
  }

  public function dayCarbon() {
    return Carbon::parse($this->day);
  }

  public $timestamps = false;
}
