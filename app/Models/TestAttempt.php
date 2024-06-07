<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Test;
use App\Models\Question;
use App\Helpers\EvalMath;

class TestAttempt extends Model {
  use HasFactory;

  protected $fillable = [
    "test_id",
    "user_id",
    "is_done",
  ];

  public function test() {
    return $this->belongsTo(Test::class);
  }

  public function entries() {
    return $this->hasMany(TestEntry::class, "attempt_id");
  }

  public function questions() {
    return $this->hasManyThrough(Question::class, TestEntry::class, "attempt_id", "id", null, "question_id");
  }

  public function entries_with_values() {
    return $this->hasMany(TestEntry::class, "attempt_id")
      ->with("question")
      ->with("answers_value")
      ->with("fields_values")
      ->with("variables_values")
      ->orderBy("created_at");
  }

  public function get_lead() {
    foreach ($this->entries as $entry) {
      $field = $entry->fields()->whereIn("type", [3, 2, 1])->withPivot('value')->first();
      if ($field) {
        return $field->pivot->value;
      }
    }
    return null;
  }

  public function is_successful_result() {
    $result = $this->test?->result;

    if ($result) {
      $checks = [
        "min_score" => boolval($result->min_score),
        "min_percent" => boolval($result->min_percent),
        "min_questions" => boolval($result->min_correct_questions),
      ];

      if ($checks["min_score"] || $checks["min_percent"] || $checks["min_questions"]) {

        $totalScore = 0;
        $totalCorrectQuestions = 0;
        $maxPossibleScore = $this->test->totalScore();

        $entries = $this->entries()->with("answers_value")->with("question")->get();

        if ($entries) {
          foreach ($entries as $entry) {
            if ($entry->question != null && in_array($entry->question->type, [1, 2])) {
              if ($entry->answers_value) {
                $question_score = 0;
                foreach ($entry->answers_value as $user_answer) {
                  $question_score += $user_answer->score;
                }
                $totalScore += $question_score;
                if ($question_score > 0) {
                  $totalCorrectQuestions++;
                }
              }
            } elseif ($entry->question != null && $entry->question->type == 5) {

              if ($entry->answers_value != null && count($entry->answers_value) > 0) {

                $answer = $entry->answers_value->first();

                $val = floatval($answer?->pivot?->value);

                include_once __DIR__ . "/../Helpers/EvalMath.php";

                $math = new EvalMath();

                foreach ($entry->variables_values as $var) {
                  $var_name = preg_replace("/[\[\]]/", "", $var->title);
                  $math->evaluate($var_name . " = " . $var->pivot->value);
                }

                if ($answer->decimals != null && $answer->decimals > 0) {
                  $correct_answer = intval($math->evaluate($answer->formula) * (10 * $answer->decimals)) / (10 * $answer->decimals);
                  $val = intval($val * (10 * $answer->decimals)) / (10 * $answer->decimals);
                } else {
                  $correct_answer = intval($math->evaluate($answer->formula));
                  $val = intval($val);
                }

                if ($correct_answer == $val) {
                  $totalScore += $answer->score;
                  $totalCorrectQuestions++;
                }
              }
            }
          }
        }

        $checks["min_score"] = $totalScore >= $result->min_score;
        $checks["min_percent"] = (($totalScore / $maxPossibleScore) * 100) >= $result->min_percent;
        $checks["min_questions"] = $totalCorrectQuestions >= $result->min_correct_questions;

        return $checks["min_score"] && $checks["min_percent"] && $checks["min_questions"];
      } else {
        return true;
      }
    } else {
      return true;
    }
  }
}
