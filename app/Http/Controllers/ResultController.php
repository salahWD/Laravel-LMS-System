<?php

namespace App\Http\Controllers;

use App\Models\Result;
use Illuminate\Http\Request;

class ResultController extends Controller {

  public function store(Request $request, $test_id) {
    $request->validate([
      "min_score" => "nullable|integer",
      "min_percent" => "nullable|integer|min:0|max:100",
      "max_attempts" => "nullable|integer|min:1",
      "min_correct_questions" => "nullable|integer|min:0",
      "custom_note" => "nullable|string",
    ]);

    if (auth()->user()->created_tests()->where("id", "=", $test_id)) {
      $res = Result::updateOrCreate([
        "test_id" => $test_id
      ], [
        "min_score" => request("min_score"),
        "min_percent" => request("min_percent"),
        "max_attempts" => request("max_attempts"),
        "min_correct_questions" => request("min_correct_questions"),
        "note" => request("custom_note"),
      ]);
    }

    return ["status" => boolval($res) ?? false];
  }

  public function edit(Result $result) {
    //
  }

  public function update(Request $request, Result $result) {
    //
  }

  public function destroy(Result $result) {
    //
  }
}
