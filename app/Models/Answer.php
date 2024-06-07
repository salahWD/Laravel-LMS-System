<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question;

class Answer extends Model {
  use HasFactory;

  public $timestamps = false;

  protected $fillable = [
    "question_id",
    "text",
    "formula",
    "decimals",
    "score",
    "order",
    "image",
  ];

  public function question() {
    return $this->belongsTo(Question::class);
  }

  public function entries() {
    return $this->belongsToMany(TestEntry::class);
  }

  public function image_url() {
    if ($this->image != null) {
      return url("images/questions/" . $this->image);
    }
    return "";
  }
}
