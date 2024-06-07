<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Answer;

class TestEntry extends Model {
  use HasFactory;

  public $fillable = [
    "attempt_id",
    "question_id",
  ];

  public function attempt() {
    return $this->belongsTo(TestAttempt::class);
  }

  public function question() {
    return $this->belongsTo(Question::class);
  }

  public function answers() {
    return $this->belongsToMany(Answer::class);
  }

  public function answers_value() {
    return $this->belongsToMany(Answer::class)->withPivot("value");
  }

  public function fields() {
    return $this->belongsToMany(Field::class, "field_entries", "entry_id");
  }

  public function fields_values() {
    return $this->belongsToMany(Field::class, "field_entries", "entry_id")->withPivot("value");
  }

  public function variables_values() {
    return $this->belongsToMany(EquationVariable::class, "entry_equation_variable", "entry_id", "equation_variable_id")->withPivot("value");
  }

  // public function fields_value() {
  //   return $this->belongsToMany(Field::class)->select("entry_field.value");
  // }

  // public function fields_label_value($lang="en") {
  //   return $this->belongsToMany(Field::class)
  //       ->select("fields.type", "entry_field.value", "field_translations.label AS field_label")
  //       ->leftJoin("field_translations", "field_translations.field_id", "=", "entry_field.field_id")
  //       ->where("locale", $lang);
  // }

  // public function answers_report() {
  //   return $this->belongsToMany(Answer::class)
  //       ->select(["answers.id", "answers.image", "en.text AS en_text", "ar.text AS ar_text"])
  //       ->join("answer_translations AS en", "en.answer_id", "=", DB::raw("answers.id AND en.locale = 'en'"))
  //       ->leftJoin("answer_translations AS ar", "ar.answer_id", "=", DB::raw("answers.id AND ar.locale = 'ar'"));
  // }

}
