<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model {
  use HasFactory;

  public $timestamps = false;

  protected $fillable = [
    "question_id",
    "is_required",
    "is_lead_email",
    "is_multiple_chooseing",
    "hidden_value",
    "format",
    "type",
    "order",
    "label",
    "placeholder",
  ];

  public function question() {
    return $this->hasMany(Question::class);
  }

  public function options() {
    return $this->hasMany(Option::class);
  }

  public function type_name() {
    if ($this->type == 3) {
      return "email";
    }
    if ($this->type == 4) {
      return "number";
    }
    if ($this->type == 5) {
      return "textarea";
    }
    if ($this->type == 7) {
      return "checkbox";
    }
    if ($this->type == 8) {
      return "select";
    }
    return "text";
  }
}
