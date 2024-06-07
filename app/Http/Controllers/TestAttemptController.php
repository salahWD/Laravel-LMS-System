<?php

namespace App\Http\Controllers;

use App\Models\EquationVariable;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\Question;
use App\Models\TestEntry;
use Illuminate\Http\Request;
use App\Helpers\EvalMath;
use App\Models\Field;

class TestAttemptController extends Controller {

  public function answering(Request $request, Test $test) {

    $request->validate([
      "id"  => "required|exists:questions,id", // id of question
      "submission_code" => "required|exists:test_attempts,id",
      "equation_question" => "sometimes|nullable|exists:questions,id",
      "answers"   => "sometimes|array|min:1",
      "answers.*" => "numeric|min:0|max:50",
    ]);

    $return = ["result" => false];

    $current_question = $test->questions()->where("id", request("id"))->first();

    if ($current_question) {
      $entry = TestEntry::firstOrCreate([
        'question_id' => $current_question->id,
        'attempt_id' => request("submission_code")
      ]);

      if ($current_question->type != 5) {
        $entry->answers()->sync(request("answers") ?? []);
      } else {

        $user_answer = request("answers");
        $val = end($user_answer);
        $val = floatval($val);

        // get the first (and only) answer of the equation question to attache it to the entry answer
        $correct_answer_obj = $current_question->answers()->first();
        $answer_id = $correct_answer_obj->id;
        $entry->answers()->attach([
          $answer_id => ["value" => $val]
        ]);

        include_once __DIR__ . "/../../Helpers/EvalMath.php";

        $math = new EvalMath();

        foreach ($entry->variables_values as $var) {
          $var_name = preg_replace("/[\[\]]/", "", $var->title);
          $math->evaluate($var_name . " = " . $var->pivot->value);
        }

        if ($correct_answer_obj->decimals != null && $correct_answer_obj->decimals > 0) {
          $correct_answer = intval($math->evaluate($correct_answer_obj->formula) * (10 * $correct_answer_obj->decimals)) / (10 * $correct_answer_obj->decimals);
          $val = intval($val * (10 * $correct_answer_obj->decimals)) / (10 * $correct_answer_obj->decimals);
        } else {
          $correct_answer = intval($math->evaluate($correct_answer_obj->formula));
          $val = intval($val);
        }

        $return["equation_result"] = boolval($val == $correct_answer);
      }

      if ($test->questions_reverse()->first()?->id == $current_question->id) {
        $has_taken_certificate = $this->mark_as_done(request("submission_code"), $test);
        $return["certificate"] = $has_taken_certificate;
      }

      $return["result"] = boolval($entry?->id);
    }

    // prepare and generate values for the next question's (equation variables)
    if (request("equation_question") != null) {
      $question = Question::find(request("equation_question"));
      $vars = [];
      foreach ($question->equationVariables as $var) {
        $var_val = $var->generate_value();
        $return["equation_variables"][$var->title] = $var_val;
        $vars[$var->id] = ["value" => $var_val];
      }
      // save the variables values for this attempt on the DB
      $entry->variables_values()->attach($vars);
    }

    return $return;
  }

  public function store(Request $request) {
    $request->validate([
      "test_id"  => "required|exists:tests,id",
      "equation_question" => "sometimes|nullable|exists:questions,id",
    ]);

    $return = [];

    $attempt = TestAttempt::create([
      "user_id" => auth()->user()->id,
      "test_id" => request("test_id"),
    ]);

    $return["submission"] = $attempt->id;

    if (request("equation_question") != null) {

      $question = Question::find(request("equation_question"));

      $vars = [];
      foreach ($question->equationVariables as $var) {
        $var_val = $var->generate_value();
        $return["equation_variables"][$var->title] = $var_val;
        $vars[$var->id] = ["value" => $var_val];
      }

      $entry = $attempt->entries()->firstOrCreate([
        'question_id' => $question->id,
      ]);

      // save the variables values for this attempt on the DB
      $entry->variables_values()->attach($vars);
    }

    return $return;
  }

  public function show(Request $request, TestAttempt $testAttempt) {
    $user_tests = auth()->user()->created_tests()->select("tests.id")->get()->pluck("id")->toArray();
    if (in_array($testAttempt->test_id, $user_tests)) {
      $testAttempt->entries_with_values;
      return $testAttempt;
    } else {
      return abort(404);
    }
  }

  public function formEntry(Request $request, Test $test) {

    $request->validate([
      "id"  => "required|exists:questions",
      "submission_code"   => "required|exists:test_attempts,id",
      "form_data"         => "sometimes|array|min:0", // to allowe skippable forms
      "form_data.*.id"    => "required|integer|exists:fields,id",
      "form_data.*.type"  => "required|integer|min:1|max:13",
    ]);

    $fields = [];
    $return = [];
    $DBfields = Field::select("fields.id", "fields.is_required", "fields.is_multiple_chooseing", "fields.is_lead_email", "fields.hidden_value", "fields.type", "questions.is_skippable")
      ->join("questions", "questions.id", "=", "fields.question_id")
      ->where("fields.question_id", request("id"))
      ->where("questions.test_id", $test->id)
      ->get();

    if (count($DBfields) > 0) {
      foreach ($DBfields as $field) {

        if ($field->is_required == 1 && $field->is_skippable != null && $field->is_skippable != 1 && count(request('form_data')) > 0) {
          if (request("form_data.$field->id.value") !== null) {
            if (in_array($field->type, [1, 2, 5, 6])) {
              $request->validate([
                "form_data.$field->id.value" => "required|string",
              ]);
            } else if ($field->type == 3) {
              $request->validate([
                "form_data.$field->id.value" => "required|email",
              ]);
            } else if (in_array($field->type, [4])) {
              $request->validate([
                "form_data.$field->id.value" => "required|regex:/[\+0-9]/i|digits_between:1,20",
              ]);
            } else if ($field->type == 7) {
              $request->validate([
                "form_data.$field->id.value" => "required|exists:options,id",
              ]);
            } else if ($field->type == 8) {
              $request->validate([
                "form_data.$field->id.value" => "required|exist:options,id",
              ]);
            }

            $fields[$field["id"]] = ["value" => request("form_data.$field->id.value")];
          } else {
            throw ValidationException::withMessages(["form_data.$field->id.value" => 'This Field is incorrect']);
          }
        } else {
          $fields[$field["id"]] = ["value" => request("form_data.$field->id.value") ?? null];
        }
      }
    }

    $entry = TestEntry::firstOrCreate([
      'question_id' => request("id"),
      'attempt_id' => request("submission_code")
    ]);

    $entry->fields()->sync($fields ?? []);

    $return["status"] = boolval($entry->id);

    if ($test->questions_reverse()->first()?->id == request("id")) {
      $has_taken_certificate = $this->mark_as_done(request("submission_code"), $test);
      $return["certificate"] = $has_taken_certificate;
    }

    if (request("equation_question") != null) {

      $question = Question::find(request("equation_question"));

      $vars = [];
      foreach ($question->equationVariables as $var) {
        $var_val = $var->generate_value();
        $return["equation_variables"][$var->title] = $var_val;
        $vars[$var->id] = ["value" => $var_val];
      }

      $entry = TestEntry::create([
        'question_id' => $question->id,
        'attempt_id' => request("submission_code"),
      ]);

      // save the variables values for this attempt on the DB
      $entry->variables_values()->attach($vars);
    }

    return $return;
  }

  public function mark_as_done($attempt_id, Test $test) {
    $attempt = TestAttempt::find($attempt_id);
    TestAttempt::where('id', $attempt_id)->update(['is_done' => 1]);
    if ($test->has_certificate()) {
      if ($attempt->is_successful_result()) {
        auth()->user()->certificates()->attach([$test->certificate_id]);
        return true;
      }
    }
    return false;
  }
}
