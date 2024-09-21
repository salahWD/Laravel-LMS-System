<?php

namespace App\Http\Controllers;

use File;
use App\Models\Test;
use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseItem;
use App\Models\Question;
use App\Models\Result;
use App\Models\Answer;
use App\Models\TestEntry;
use Illuminate\Support\Facades\DB;

class TestController extends Controller {

  public function index() {
    if (auth()->user()->is_admin()) {
      $tests = Test::paginate(config('settings.tables_row_count'));
    } else {
      $tests = auth()->user()->created_tests();
    }
    return view("dashboard.tests.index", compact("tests"));
  }

  public function create(Course $course = null) {
    $courses = auth()->user()->courses;
    if ($course != null) {
      return view("dashboard.tests.create", compact(["courses", "course"]));
    } else {
      return view("dashboard.tests.create", compact(["courses"]));
    }
  }

  public function store(Request $request) {
    $request->validate([
      "title" => "required|string|max:255",
      "course_id" => "present|nullable|integer|exists:courses,id",
      // "thumbnail" => "nullable|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte
    ]);

    $testInfo = [
      "title" => request("title"),
      // "course_id" => request("course_id") ?? null,
    ];
    // if ($request->hasFile("thumbnail")) {
    //   // upload image
    //   $image = date('mdYHis') . uniqid() . substr($request->file('thumbnail')->getClientOriginalName(), -10);
    //   $request->thumbnail->move(public_path('images/tests'), $image);
    //   $testInfo["thumbnail"] = $image;
    // }

    $test = Test::create($testInfo);
    if (request("course_id") != null) {
      $test->courseItem()->create([
        "course_id" => request("course_id"),
        "order" => CourseItem::generate_order(request("course_id")),
      ]);
    }

    return redirect()->route("test_build", $test->id);
  }

  public function build(Test $test) {
    $result_types = [
      ["id" => 1, "icon" => "fa-certificate fa-lg", "title" => __("Certificate")],
    ];
    $question_types = [
      ["id" => 1, "icon" => "fa-text-width", "title" => __("Text Question")],
      ["id" => 2, "icon" => "fa-picture-o", "title" => __("Image Question")],
      ["id" => 3, "icon" => "fa-tasks", "title" => __("Form Fields")],
      ["id" => 4, "icon" => "fa-file-video-o", "title" => __("Image Or Video")],
      ["id" => 5, "icon" => "fa-subscript fa-lg", "title" => __("Equation")],
    ];
    $certificates_types = Certificate::get_themes();
    $available_certificates = auth()->user()->created_certificates;
    return view("dashboard.tests.build", compact("test", "question_types", "result_types", "certificates_types", "available_certificates"));
  }

  public function certificates(Request $request, Test $test) {
    $request->validate([
      "certificate_id" => "required_if:ar_title,null|nullable|integer|exists:certificates,id",
      "ar_title" => "required_if:certificate_id,null|nullable|string",
      "ar_desc" => "sometimes|nullable|string",
      "theme" => "required_if:certificate_id,null|nullable|string|in:" . implode(",", Certificate::get_themes()),
    ]);

    if (request("certificate_id") != null && auth()->user()->have_created_certificate(request("certificate_id"))) {
      $test->certificate_id = request("certificate_id");
      $certificate = Certificate::find(request("certificate_id"));
    } else {
      $certificate = Certificate::create([
        "title" => request("ar_title"),
        "user_id" => auth()->user()->id,
        "description" => request("ar_desc"),
        "template" => request("theme"),
      ]);
      $test->certificate_id = $certificate->id;
    }
    $res = $test->save();

    return ["status" => $res, "id" => $certificate->id, "title" => $certificate->title];
  }

  public function certificate_delete(Test $test) {
    if (auth()->user()->have_created_certificate($test->certificate->id)) {
      $test->certificate_id = null;
      $res = $test->save();
    }
    return ["status" => $res ?? false];
  }

  public function show(Test $test) {
    $no_header_footer = true;

    if ($test->open_for_user(auth()->user()->id)) {

      $questions = Question::where("test_id", $test->id)
        ->with("answers")
        ->with("fields_with_options")
        ->orderBy("questions.order")
        ->get();

      $result = $test->result ?? null;

      return view("test")->with([
        "current_test" => $test,
        "questions" => $questions,
        "result" => $result,
        "no_header_footer" => $no_header_footer,
      ]);
    } else {
      abort(404);
    }
  }

  public function delete_intro_image(Test $test) {

    if (auth()->user()->is_admin() || auth()->user()->created_tests()->where("tests.id", "=", $test->id)->count() > 0) {

      if ($test->intro_image != null && File::exists(public_path("images/tests/$test->intro_image"))) {
        File::delete(public_path("images/tests/$test->intro_image"));
      }
      $test->intro_image = null;
      $res = $test->save();
    }
    return ["status" => $res ?? false];
  }

  public function copy($test_id) {

    $test_info = Test::where("id", $test_id)
      ->with("questions")
      ->with("questions.answers")
      ->with("results")
      ->first();

    $copy = $test_info->replicate();

    $copy->status = 1;

    if ($test_info->image != NULL && File::exists(public_path("images/tests/$test_info->image"))) {
      $unique_str = date('mdYHis') . uniqid();
      $new_image_name = $unique_str . substr($test_info->image, strlen($unique_str));
      File::copy(public_path("images/tests/$test_info->image"), public_path("images/tests/$new_image_name"));
      $copy->image = $new_image_name;
    }

    if ($test_info->intro_image != NULL && File::exists(public_path("images/tests/$test_info->intro_image"))) {
      $unique_str = date('mdYHis') . uniqid();
      $new_image_name = $unique_str . substr($test_info->intro_image, strlen($unique_str));
      File::copy(public_path("images/tests/$test_info->intro_image"), public_path("images/tests/$new_image_name"));
      $copy->intro_image = $new_image_name;
    }

    $copy->save();

    /* ====== replicate results ====== */
    if ($test_info->results->count() > 0) {
      foreach ($test_info->results as $i => $result) {

        $newResult = $result->replicat();
        $newResult->test_id = $copy->id;
        $newResult->save();
      }
    }

    /* ====== replicate questions ====== */
    if ($test_info->questions->count() > 0) {

      foreach ($test_info->questions as $question) {

        $copyQuestion = $question->replicate();

        /* ====== copy questino image ====== */
        if (in_array($copyQuestion->type, [1, 2, 3, 4])) {

          if ($copyQuestion->image != NULL && File::exists(public_path("images/questions/$copyQuestion->image"))) {
            $unique_str = date('mdYHis') . uniqid();
            $new_image_name = $unique_str . substr($copyQuestion->image, -10);
            File::copy(public_path("images/questions/$copyQuestion->image"), public_path("images/questions/$new_image_name"));
            $copyQuestion->image = $new_image_name;
          }
        }

        $copyQuestion->test_id = $copy->id;

        $copyQuestion->save();

        /* ====== replicate answers ====== */
        if (in_array($question->type, [1, 2])) {
          if ($question->answers->count() > 0) {

            foreach ($question->answers as $answer) {

              $duplicatedAnswer = $answer->replicate();

              if ($question->type == 2) {
                if ($duplicatedAnswer->image != NULL && File::exists(public_path("images/questions/$duplicatedAnswer->image"))) {
                  $unique_str = date('mdYHis') . uniqid();
                  $new_image_name = $unique_str . substr($duplicatedAnswer->image, -10);
                  File::copy(public_path("images/questions/$duplicatedAnswer->image"), public_path("images/questions/$new_image_name"));
                  $duplicatedAnswer->image = $new_image_name;
                }
              }

              $duplicatedAnswer->question_id = $copyQuestion->id;

              $duplicatedAnswer->save();
            }
          }
        }
      }
    }

    return redirect()->route("build_test", $copy->id);
  }

  public function update_name(Test $test, Request $request) {
    $request->validate([
      'title' => 'required|string|min:1',
    ]);

    $test->title = request("title");

    return $test->save();
  }

  public function update(Test $test, Request $request) {
    $request->validate([
      'has_intro' => 'sometimes|in:on,1,true,no,off,0,false',
      'intro_title' => 'required_if_accepted:has_intro|nullable|string',
      'intro_desc' => 'nullable|string',
      'intro_btn' => 'nullable|string',
      "intro_image" => "nullable|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte
    ]);

    $test->has_intro          = request("has_intro") != null ? 1 : 0;
    $test->intro_title        = request("intro_title");
    $test->intro_description  = request("intro_desc");
    $test->intro_btn          = request("intro_btn");

    if ($request->hasFile('intro_image')) {
      // delete old intro_image
      if ($test->intro_image != NULL && File::exists(public_path("images/tests/$test->intro_image"))) {
        File::delete(public_path("images/tests/$test->intro_image"));
      }
      // upload new intro_image
      $test->intro_image = date('mdYHis') . uniqid() . substr($request->file('intro_image')->getClientOriginalName(), -10);
      $request->intro_image->move(public_path('images/tests'), $test->intro_image);
    }

    return ["status" => $test->save()];
  }

  public function update_status(Test $test, Request $request) {

    $request->validate([
      'publish' => 'required|integer|in:1,0',
    ]);

    $test->status = request("publish");

    return $test->save();
  }

  public function report($test_id) {

    $questions = Question::where("test_id", $test_id)
      ->withCount("testEntries")
      ->with("answers")
      ->orderBy("questions.order")
      ->get();

    $result = Result::where("test_id", $test_id)->first();

    $test = Test::find($test_id)->withCount("testAttempts")->first();

    $responses = $test->test_attempts_count;
    $completed = $test->testAttempts()->where("is_done", 1)->count();

    return view("dashboard.tests.report")->with(compact(
      "test",
      "questions",
      'result',
      'responses',
      'completed',
    ));
  }




  public function export(Test $test, Request $request) {

    if (auth()->user()->is_admin() || auth()->user()->id == $test->website->user_id) {
      if (isset($request["exports"]) && !empty($request["exports"])) {


        $ids = explode(",", $request["exports"]);
        $submissions = Submission::whereIn("id", $ids)->get();

        $leads = [];
        // $submissions = $test->nonempty_submissions()->whereIn("id", )->get();
        foreach ($submissions as $submission) {
          // $a = $submission->entries;
          // $b = $submission->entries_with_questions_answers;

          $info = [];
          $entries = $submission->report_entry();

          $info["id"] = $submission->id;
          $info["date"] = $submission->created_at->toDateTimeString();

          $q_count = 0;

          if ($entries->count() > 0) {

            foreach ($entries as $i => $entry) {

              if (!empty($entry->utm_name)) {
                $info[$entry->utm_name] = $entry->utm_value;
              }
              if (!empty($entry->en_question_title)) {
                $info[$i . "_" . $entry->en_question_title] = '';
                if ($entry->fields_value->count() > 0) {
                  foreach ($entry->fields_value as $answer) {
                    $info[$i . "_" . $entry->en_question_title] .= $answer->value;
                  }
                }
                if ($entry->answers->count() > 0) {
                  foreach ($entry->answers as $answer) {
                    $info[$i . "_" . $entry->en_question_title] .= $answer->text;
                  }
                }
              }
            }
          }

          array_push($leads, $info);
        }

        if (count($leads) > 0) {

          $fileName = time() . '_leads_export.csv';
          $headers = array(
            "Content-type"        => "text/csv;charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
          );

          $columns = array_keys($leads[0]);

          $callback = function () use ($leads, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($leads as $lead) {
              fputcsv($file, array_values($lead));
            }
            fclose($file);
          };

          return response()->stream($callback, 200, $headers);
        }

        /* ======== JSON EXPORT ======== */
        // if (count($leads) > 0) {

        //   $json_data = json_decode($leads);
        //   $fileName = time() . '_leads_export.json';
        //   $fileStorePath = public_path('exports/' . $fileName);
        //   $file = File::put($fileStorePath, $json_data);

        //   $headers = array(
        //     'Content-Type: application/json',
        //   );
        //   if ($file) {
        //     File::delete($fileStorePath);
        //     return response()->download(
        //       $fileStorePath,
        //       $fileName,
        //       $headers
        //     );
        //   }
        // }

      }

      return redirect()->route("report_test", $test->id);
    }
  }

  public function get_submission($submission_id) {

    $submission = Submission::where("id", $submission_id)->with("result")->get()->first();

    if ($submission != null) {
      $entries = $submission->report_entry();

      if ($entries->count() > 0) {
        foreach ($entries as $entry) {
          if (count($entry->answers_report) > 0) {
            $entry->answers_report;
          }
          if (count($entry->fields_label_value) > 0) {
            $fields = $entry->fields_label_value;
            foreach ($fields as $field) {
              if (in_array($field->type, [8])) {
                $field->value = Option::find($field->value)->value;
              }
            }
          }
        }
      }

      $submission->entries = $entries;
      return $submission;
    } else {
      return abort(404);
    }
  }



  public function destroy(Request $request, Test $test) {

    $request->validate([
      "page" => "integer:min:0",
    ]);

    $course_id = $test->course_id;
    $test_images = Test::select("id", "thumbnail", "intro_image")->where("id", $test->id)->first();

    $questions_images = Question::select("questions.id", "questions.image")
      ->where("test_id", $test->id)
      ->whereNotNull("image")
      ->get();

    $answers_images = Answer::select("answers.id", "answers.image")
      ->join("questions", "questions.id", "=", "answers.question_id")
      ->where("questions.test_id", $test->id)
      ->whereNotNull("answers.image")
      ->get();

    if ($test_images->image != null && File::exists(public_path("images/tests/$test_images->image"))) {
      File::delete(public_path("images/tests/$test_images->image"));
    }
    if ($test_images->intro_image != null && File::exists(public_path("images/tests/$test_images->intro_image"))) {
      File::delete(public_path("images/tests/$test_images->intro_image"));
    }
    foreach ([...$answers_images, ...$questions_images] as $image) {
      if ($image->image != null && File::exists(public_path("images/questions/$image->image"))) {
        File::delete(public_path("images/questions/$image->image"));
      }
    }

    $per_page = config('settings.tables_row_count'); // lecture pagination
    $next_test = auth()->user()->courses()->orderBy("created_at", "DESC")->skip($per_page * request("page"))->first();

    if ($test->courseItem != null) {
      $test->courseItem->delete();
    }
    $res = $test->delete();

    // if ($next_test != null) {
    //   $next_test = [
    //     "id" => $next_test->id,
    //     "title" => $next_test->title,
    //     "description" => $next_test->description,
    //     "order" => $next_test->order,
    //     "status" => $next_test->image_url(),
    //     "category_title" => $next_test?->category?->title,
    //     "status_icon" => $next_test->status_icon(),
    //     "image" => $next_test->image_url(),
    //     "date" => $next_test->get_date(),
    //   ];
    // }

    return ["result" => $res, "next_test" => $next_test];
  }
}
