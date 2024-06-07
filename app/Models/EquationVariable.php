<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Question;

class EquationVariable extends Model {
  use HasFactory;

  public $timestamps = false;

  protected $fillable = [
    "question_id",
    "title",
    "min_range",
    "max_range",
    "decimal",
  ];

  public function question() {
    return $this->belongsTo(Question::class);
  }

  public function variables_values() {
    return $this->belongsToMany(TestEntry::class, "entry_equation_variable", "equation_variable_id", "entry_id")->withPivot("value");
  }

  public function generate_value() {
    if ($this->min_range != null && $this->max_range != null) {
      if ($this->decimal != null) {
        return rand($this->min_range * (10 * $this->decimal), $this->max_range * (10 * $this->decimal)) / (10 * $this->decimal);
      } else {
        return rand($this->min_range, $this->max_range);
      }
    } else {
      return 1;
    }
  }
}
