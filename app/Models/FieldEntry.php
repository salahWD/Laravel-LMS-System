<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldEntry extends Model {
  use HasFactory;

  public $fillable = [
    "field_id",
    "value",
  ];

  public function field() {
    return $this->belongsTo(Field::class);
  }
}
