<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model {
  use HasFactory;

  public $timestamps = false;

  protected $fillable = [
    "test_id",
    "min_score",
    "min_percent",
    "max_attempts",
    "min_correct_questions",
    "note",
  ];

  public function test() {
    return $this->belongsTo(Test::class);
  }
}
