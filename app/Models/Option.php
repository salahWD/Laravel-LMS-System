<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model {
  use HasFactory;

  public $timestamps = false;

  protected $fillable = [
    "field_id",
    "value",
  ];

  function field() {
    return $this->belongsTo(Field::class);
  }
}
