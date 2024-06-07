<?php

namespace App\Http\Controllers;

use File;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Answer;
use App\Models\Field;

class QuestionController extends Controller {

  public function __construct() {
    $this->middleware('auth')->except("index");
  }

  public function index() {
    dd(request()->all(), "method: index => Question Controller");
  }

  public function store(Request $request, Test $test) {
    if (auth()->user()->is_admin() || auth()->user()->can_manage_course(auth()->user()->id, $test->course_id)) {

      $request->validate([
        "type_id" => "required|integer|min:1|max:5",
        "ar_question_title"  => "required|max:200",
        "ar_question_desc"   => "nullable|max:400",
        // "en_question_title"  => "sometimes|required|string|max:200",
        // "en_question_desc"   => "sometimes|nullable|max:400",
      ]);

      if (in_array(request("type_id"), [1, 2])) {
        $validate_ruls = [
          "image"               => "nullable|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte
          "video"               => "nullable|url",
          "ar_answers"          => "required|array|min:1",
          "ar_answers.*.image"  => "sometimes|nullable|image|mimes:png,jpg,jpeg|max:2048",
          "ar_answers.*.text"   => "required",
          "ar_answers.*.ar_text" => "sometimes|nullable",
          "ar_answers.*.score"  => "nullable|integer",
          "ar_answers.*.order"  => "required|integer|min:1",
          "order"               => "required|integer|min:1",
          "is_multi_select"        => "required|boolean",
        ];

        if (request("type_id") == 2) {

          $validate_ruls["ar_answers.*.image"] = 'required|image|mimes:png,jpg,jpeg|max:2048';
        }
      } elseif (request("type_id") == 3) {
        $validate_ruls = [
          "media_type"      => "required|in:image,video",
          "video"           => "nullable|exclude_if:media_type,image|url",
          "image"           => "nullable|exclude_if:media_type,video|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte",
          "ar_button_label"    => "required|string|max:120",
          // "en_button_label"    => "sometimes|string|max:120",
          "is_skippable"    => "required|in:true,false",
          "order"           => "required|integer|min:1",
          "fields"            => "required|array|max:16",
          "fields.*.label"    => "required|string|max:120",
          "fields.*.order"    => "required|string|max:120",
          "fields.*.placeholder" => "nullable|string|max:120",
          "fields.*.type"     => "required|integer|min:1|max:8",
        ];
      } elseif (request("type_id") == 4) {
        $validate_ruls = [
          "media_type"      => "nullable|in:image,video,no",
          "video"           => "required_if:media_type,video|exclude_if:media_type,image,no|url",
          "image"           => "required_if:media_type,image|exclude_if:media_type,video,no|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte",
          "ar_button_label"    => "required|max:120",
          // "en_button_label"    => "sometimes|nullable|max:120",
          "order"           => "required|integer|min:1",
        ];
      } elseif (request("type_id") == 5) {
        $validate_ruls = [
          "image"                 => "nullable|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte
          "score"                 => "required|integer",
          "answer_equation"       => "required|string",
          "answer_equation_formula" => "required|string",
          "answer_decimals"       => "present|nullable|integer|max:10|min:0",
          "variables"             => "nullable|array|min:1",
          "variables.*.name"      => "required",
          "variables.*.decimal"   => "nullable|integer",
          "variables.*.min"       => "required|integer",
          "variables.*.max"       => "required|integer|min:1",
          "is_skippable"          => "required|string|in:true,false",
        ];
      }

      $request->validate($validate_ruls);

      $questino_info = [
        "test_id" => $test->id,
        "title" => request("ar_question_title"),
        "description" => request("ar_question_desc"),
        "type" => request("type_id"),
      ];

      if (in_array(request("type_id"), [1, 2])) {

        if ($request->hasFile('image')) {
          $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . substr($request->file('image')->getClientOriginalName(), -10));
          $request->image->move(public_path('images/questions'), $unique_name);
        }

        $questino_info["image"] = $unique_name ?? null;
        $questino_info["is_multi_select"] = $request->boolean("is_multi_select");
        $questino_info["order"] = request("order");

        // "ar" => [
        //   "title" => request("ar_question_title"),
        //   "description" => request("ar_question_desc"),
        // ],

        // if (!empty(request("en_question_title"))) {
        //   $questionInfo["en"] = [
        //     "title" => request("en_question_title"),
        //     "description" => request("en_question_desc"),
        //   ];
        // }
        $question = Question::create($questino_info);

        // Answers Handlers
        if (request("type_id") == 1) { // text question

          foreach (request("ar_answers") as $answer) {
            $answerInfo = [
              "question_id" => $question->id,
              'text' => $answer["text"],
              "score" => $answer["score"],
              "order" => $answer["order"]
            ];
            // if (!empty($answer["en_text"])) {
            //   $answerInfo['en'] = ['text' => $answer["en_text"]];
            // }
            Answer::create($answerInfo);
          }
        } elseif (request("type_id") == 2) { // image question

          foreach (request("ar_answers") as $i => $answer) {

            $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . substr($answer["image"]->getClientOriginalName(), -10));
            $answer["image"]->move(public_path('images/questions'), $unique_name);
            $answerInfo = [
              "question_id" => $question->id,
              'text' => $answer["text"],
              "score" => $answer["score"],
              "order" => $answer["order"],
              "image" => $unique_name
            ];
            // if (!empty($answer["en_text"])) {
            //   $answerInfo['en'] = ['text' => $answer["en_text"]];
            // }
            Answer::create($answerInfo);
          }
        }
      } elseif (request("type_id") == 3) {

        $questino_info["button_label"] = request("ar_button_label");
        $questino_info["is_required"] = request("is_required") == "true" ? 1 : 0;
        $questino_info["is_skippable"] = request("is_skippable") == "true" ? 1 : 0;
        $questino_info["order"] = request("order");


        // "ar" => [
        //   "title" => request("ar_question_title"),
        //   "description" => request("ar_question_desc"),
        //   "button_label" => request("ar_button_label"),
        // ],

        // if (!empty(request("en_question_title"))) {
        //   $questino_info["en"] = [
        //     "title" => null !== request("en_question_title") && !empty(request("en_question_title")) ? request("en_question_title") : "",
        //     "description" => null !== request("en_question_desc") && !empty(request("en_question_desc")) ? request("en_question_desc") : "",
        //     "button_label" => null !== request("en_button_label") && !empty(request("en_button_label")) ? request("en_button_label") : "",
        //   ];
        // }

        if (request("media_type") == "video" && isset($request["video"])) {
          $questino_info["video"] = $request["video"];
        } elseif (request("media_type") == "image" && isset($request["image"])) {
          $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . substr($request["image"]->getClientOriginalName(), -10));
          $request["image"]->move(public_path('images/questions'), $unique_name);
          $questino_info["image"] = $unique_name;
        }

        $question = Question::create($questino_info);

        foreach (request("fields") as $field) {

          $field_obj = [
            "question_id" => $question->id,
            "order" => $field["order"],
            "type" => $field["type"],
            "label" => $field["label"],
            // "ar" => [
            //   "label" => $field["label"],
            // ],
          ];

          if (in_array($field["type"], [1, 2, 3, 4, 5, 6, 8])) {
            $field_obj["placeholder"] = $field["placeholder"];
            // if (!empty($field["ar_label"])) {
            //   $field_obj["en"]["label"] = $field["en_label"];
            //   $field_obj["en"]["placeholder"] = $field["en_placeholder"];
            // }
          }
          if ($field["type"] == 3) {
            $field_obj["is_lead_email"] = $field["is_lead_email"];
          }
          $field_obj["is_required"] = $field["is_required"];

          $new_field = Field::create($field_obj);

          if (in_array($field["type"], [7, 8])) {
            $options = [];
            foreach ($field["options"] as $option) {
              array_push($options, [
                "field_id" => $new_field->id,
                "value" => $option,
              ]);
            }
            $new_field->options()->createMany($options);
          }
        }
      } elseif (request("type_id") == 4) {

        $questino_info["button_label"] = request("ar_button_label");
        $questino_info["order"] = request("order");

        // image or video upload
        if (request("media_type") == "video") {
          $questino_info["video"] = request("video");
        } elseif (request("media_type") == "image") {
          $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . substr(request("image")->getClientOriginalName(), -10));
          request("image")->move(public_path('images/questions'), $unique_name);
          $questino_info["image"] = $unique_name;
        }

        $question = Question::create($questino_info);
      } elseif (request("type_id") == 5) {

        // image upload
        if (request("image") != null) {
          $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . substr(request("image")->getClientOriginalName(), -10));
          request("image")->move(public_path('images/questions'), $unique_name);
          $questino_info["image"] = $unique_name;
        }

        $questino_info["is_skippable"] = request("is_skippable") == "true" ? 1 : 0;
        $questino_info["order"] = request("order");

        $question = Question::create($questino_info);

        $question->answers()->create([
          "score" => request("score"),
          "text" => request("answer_equation"),
          "formula" => request("answer_equation_formula"),
          "order" => 1,
          "decimals" => request("answer_decimals"),
        ]);

        if (request("variables") != null && count(request("variables")) > 0) {
          $variables = [];
          foreach (request("variables") as $variable) {
            $variables[] = [
              "title" => $variable["name"],
              "decimal" => $variable["decimal"],
              "min_range" => $variable["min"],
              "max_range" => $variable["max"],
            ];
          }
          $question->equationVariables()->delete();
          $question->equationVariables()->createMany($variables);
        }
      }

      return $question->id;
    } else {
      return false;
    }
  }

  public function show(Question $question) {
    if (auth()->user()->is_admin() || auth()->user()->can_manage_course(auth()->user()->id, $question->test->course_id)) {

      if (in_array($question->type, [1, 2])) {
        $question->answers;
      } elseif ($question->type == 3) {
        $question->fields = $question->fields_with_options;
      } elseif ($question->type == 5) {
        $question->equationVariables;
        $question->answers;
      }

      return $question;
    } else {
      if ($question->test->status == 1) { // quiz is publish

        if (in_array($question->type, [1, 2])) {
          $question->answers;
        } elseif ($question->type == 3) {
          $question->fields = $question->fields_with_options;
        } elseif ($question->type == 5) {
          $question->equationVariables;
          $question->answers;
        }

        return $question;
      }
    }
    return false;
  }

  public function copy(Question $question, Request $request) {

    $order = $question->largest_order();
    $newQuestion = $question->replicate();
    $newQuestion->order = $order + 1;

    if ($question->image != NULL && File::exists(public_path("images/questions/$question->image"))) {
      $unique_str = date('mdYHis') . uniqid();
      $new_image_name = $unique_str . substr($question->image, -10);
      File::copy(public_path("images/questions/$question->image"), public_path("images/questions/$new_image_name"));
      $newQuestion->image = $new_image_name;
    }

    $newQuestion->save();

    if (in_array($question->type, [1, 2])) {
      $answres = [];

      if (count($question->answers) > 0) {
        foreach ($question->answers as $answer) {
          $duplicatedAnswer = $answer->replicate();
          $duplicatedAnswer->question_id = $newQuestion->id;
          $answres[] = $duplicatedAnswer;
        }
      }

      $newQuestion->answers()->saveMany($answres);
    }

    return $newQuestion->id;
  }

  public function update(Request $request, Question $question) {

    $request->validate([
      "type_id" => "required|integer|min:1|max:5",
      "ar_question_title"  => "required|max:200",
      "ar_question_desc"   => "max:400",
      // "en_question_title"  => "sometimes|required|string|max:200",
      // "en_question_desc"   => "sometimes|nullable|max:400",
    ]);

    if (in_array(request("type_id"), [1, 2])) {
      $validate_ruls = [
        "image"               => "nullable|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte
        "video"               => "nullable|url",
        "ar_answers"          => "required|array|min:1",
        "ar_answers.*.id"     => "nullable|integer|exists:answers",
        "ar_answers.*.text"   => "required",
        "ar_answers.*.score"  => "required|integer",
        "ar_answers.*.order"  => "required|integer|min:1",
        "ar_answers.*.action" => "sometimes|in:remove",
        "is_multi_select"        => "required|boolean",
      ];
    } else if (request("type_id") == 3) {
      $validate_ruls = [
        "media_type"      => "required|in:image,video",
        "video"           => "nullable|exclude_if:media_type,image|url",
        "image"           => "nullable|exclude_if:media_type,video|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte",
        "ar_button_label" => "required|string|max:120",
        // "en_button_label" => "sometimes|string|max:120",
        "is_skippable"    => "required|in:true,false",
        "fields"          => "required|array|max:16",
        "fields.*.id"     => "sometimes|required|integer|exists:fields,id",
        "fields.*.label"  => "required|string|max:120",
        "fields.*.order"  => "required|string|max:120",
        "fields.*.placeholder"  => "nullable|string|max:120",
        "fields.*.type"         => "required|integer|min:1|max:8",
        "fields.*.is_multiple_chooseing" => "required_if:fields.*.type,7|integer|in:0,1",
        "fields.*.options"      => "required_if:fields.*.type,7|array|min:1",
        "fields.*.options.*"    => "required_if:fields.*.type,7|string|max:255",
      ];
    } else if (request("type_id") == 4) {
      $validate_ruls = [
        "media_type"      => "required|in:image,video,no",
        "video"           => "required_if:media_type,video|url",
        "image"           => "required_if:media_type,image|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte",
        "ar_button_label"    => "required|max:120",
        // "en_button_label"    => "sometimes|string|max:120",
      ];
    } elseif (request("type_id") == 5) {
      $validate_ruls = [
        "image"                 => "nullable|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte
        "score"                 => "required|integer",
        "answer_equation"       => "required|string",
        "answer_equation_formula" => "required|string",
        "answer_decimals"       => "present|nullable|integer|max:10|min:0",
        "variables"             => "nullable|array|min:1",
        "variables.*.name"      => "required",
        "variables.*.decimal"   => "nullable|integer",
        "variables.*.min"       => "required|integer",
        "variables.*.max"       => "required|integer|min:1",
        "is_skippable"          => "required|string|in:true,false",
      ];
    }

    $request->validate($validate_ruls);

    if (in_array(request("type_id"), [1, 2])) {

      if ($request->hasFile('image')) {

        $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . substr($request->image->getClientOriginalName(), -10));
        $request->image->move(public_path('images/questions'), $unique_name);
        $old_image = $question->image;
        $question->image = $unique_name;

        if (File::exists(public_path("images/questions/") . $old_image)) {
          File::delete(public_path("images/questions/") . $old_image);
        }
      }

      $question->video = request("video");
      $question->title       = request("ar_question_title");
      $question->description = request("ar_question_desc");
      $question->is_multi_select = $request->boolean("is_multi_select");

      // Answers Handlers
      $answers = $question->answers;

      if (request("type_id") == 1) { // text question

        foreach (request("ar_answers") as $answer) {
          if (isset($answer["id"]) && !empty($answer["id"]) && is_numeric((int)$answer["id"])) {
            foreach ($answers as $DBAnswer) {
              if ($DBAnswer->id == $answer["id"]) {
                if (isset($answer["action"]) && $answer["action"] == "remove") {
                  $DBAnswer->delete();
                } else {
                  $DBAnswer->text = $answer["text"];
                  $DBAnswer->score = $answer["score"];
                  $DBAnswer->order = $answer["order"];
                  $DBAnswer->save();
                }
              }
            }
          } else {
            $createInfo = [
              "question_id" => $question->id,
              'text' => $answer["text"],
              "score" => $answer["score"],
              "order" => $answer["order"]
            ];
            Answer::create($createInfo);
          }
        }
      } elseif (request("type_id") == 2) { // image question

        foreach (request("ar_answers") as $i => $answer) {

          if (isset($answer["id"]) && !empty($answer["id"]) && is_numeric((int)$answer["id"])) {
            foreach ($answers as $DBAnswer) {
              if ($DBAnswer->id == $answer["id"]) {
                if (isset($answer["action"]) && $answer["action"] == "remove") {
                  $DBAnswer->delete();
                  if ($DBAnswer->image != null && File::exists(public_path("images/questions/") . $DBAnswer->image)) {
                    File::delete(public_path("images/questions/") . $DBAnswer->image);
                  }
                } else {
                  $DBAnswer->text = $answer["text"];
                  $DBAnswer->score = $answer["score"];
                  $DBAnswer->order = $answer["order"];

                  if ($request->hasFile("en_answers.$i")) {
                    $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . substr($answer["image"]->getClientOriginalName(), -10));
                    $answer["image"]->move(public_path('images/questions'), $unique_name);
                    $old_image = $DBAnswer->image;
                    if ($old_image != null && File::exists(public_path("images/questions/") . $old_image)) {
                      File::delete(public_path("images/questions/") . $old_image);
                    }
                    $DBAnswer->image = $unique_name;
                  }

                  $DBAnswer->save();
                }
              }
            }
          } else {

            $request->validate([
              "en_answers.$i.image" => "required|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte
            ]);

            $unique_name = "default.jpg";
            if ($request->hasFile("en_answers.$i")) {
              $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . substr($answer["image"]->getClientOriginalName(), -10));
              $answer["image"]->move(public_path('images/questions'), $unique_name);
            }

            Answer::create([
              "question_id" => $question->id,
              "text" => $answer["text"],
              "score" => $answer["score"],
              "order" => $answer["order"],
              "image" => $unique_name
            ]);
          }
        }
      }
    } else if (request("type_id") == 3) {

      if (request("media_type") == "video" && isset($request["video"])) {
        $question->video = $request["video"];
        $question->image = NULL;
      } elseif (request("media_type") == "image" && isset($request["image"])) {
        if ($question->image != NULL && File::exists(public_path("images/questions/$question->image"))) {
          File::delete(public_path("images/questions/$question->image"));
        }
        $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . substr($request["image"]->getClientOriginalName(), -10));
        $request["image"]->move(public_path('images/questions'), $unique_name);
        $question->image = $unique_name;
        $question->video = NULL;
      }

      $question->title        = $request["ar_question_title"];
      $question->description  = $request["ar_question_desc"];
      $question->button_label = $request["ar_button_label"];
      $question->is_skippable = $request["is_skippable"] == "true" ? 1 : 0;

      $fields_ids = [];
      $fields = $question->fields;
      $DbFieldsIds = $fields->pluck("id")->toArray();

      foreach (request("fields") as $field) {
        if (isset($field["id"]) && null !== $field["id"]) {
          if (in_array($field["id"], $DbFieldsIds)) {

            $value = $fields->filter(function ($obj) use ($field) {
              return $obj->id == $field["id"];
            })->first();

            if ($value !== null && !empty($value)) {

              $value->label = $field["label"];
              $value->order = $field["order"];

              if (in_array($value->type, [1, 2, 3, 4, 5, 6, 8])) { // placeholder
                $value->placeholder = $field["placeholder"];
              }

              $value->is_required = $field["is_required"];
              if ($field["type"] == 3) {
                $value->is_lead_email = $field["is_lead_email"];
              }
              if ($field["type"] == 7) {
                $value->is_multiple_chooseing = $field["is_multiple_chooseing"];
              }

              $value->save();

              array_push($fields_ids, $value->id);

              if (in_array($field["type"], [7, 8])) {
                $options = [];
                foreach ($field["options"] as $option) {
                  array_push($options, [
                    "field_id" => $value->id,
                    "value" => $option,
                  ]);
                }
                $value->options()->delete();
                $value->options()->createMany($options);
              }
            }
          }
        } else {

          $field_obj = [
            "question_id" => $question->id,
            "order" => $field["order"],
            "type" => $field["type"],
            "label" => $field["label"],
          ];

          if (in_array($field["type"], [1, 2, 3, 4, 5, 6, 8])) {
            $field_obj["placeholder"] = $field["placeholder"];
          }
          $field_obj["is_required"] = $field["is_required"];
          if ($field["type"] == 3) {
            $field_obj["is_lead_email"] = $field["is_lead_email"];
          }
          if ($field["type"] == 7) {
            $field_obj["is_multiple_chooseing"] = $field["is_multiple_chooseing"];
          }

          $new_field = Field::create($field_obj);
          array_push($fields_ids, $new_field->id);

          if (in_array($field["type"], [7, 8])) {
            $options = [];
            foreach ($field["options"] as $option) {
              array_push($options, [
                "field_id" => $new_field->id,
                "value" => $option,
              ]);
            }
            $new_field->options()->createMany($options);
          }
        }
      }

      Field::where("question_id", $question->id)->whereNotIn('id', $fields_ids)->delete();
    } else if (request("type_id") == 4) {

      if (request("media_type") == "image") {
        if (!isset($request["image"]) || $request["image"] == NULL) {
          if ($question->image == NULL) {
            $validatedImage = $request->validate([
              "image" => "required|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte",
            ]);
          }
        } else {
          if ($question->image != NULL && File::exists(public_path("images/questions/$question->image"))) {
            File::delete(public_path("images/questions/$question->image"));
          }
          $image = $request["image"];
          $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . substr($image->getClientOriginalName(), -10));
          $image->move(public_path('images/questions'), $unique_name);
          $question->image = $unique_name;
          $question->video = NULL;
        }
      } elseif (request("media_type") == "video") {
        $question->video = $request["video"];
        if ($question->image != NULL && File::exists(public_path("images/questions/$question->image"))) {
          File::delete(public_path("images/questions/$question->image"));
        }
        $question->image = NULL;
      }

      $question->title        = request("ar_question_title");
      $question->description  = request("ar_question_desc");
      $question->button_label = request("ar_button_label");
    } else if (request("type_id") == 5) {

      if (request("image") != NULL) {
        if ($question->image != NULL && File::exists(public_path("images/questions/$question->image"))) {
          File::delete(public_path("images/questions/$question->image"));
        }
        $image = request("image");
        $unique_name = str_replace(" ", "", date('mdYHis') . uniqid() . substr($image->getClientOriginalName(), -10));
        $image->move(public_path('images/questions'), $unique_name);
        $question->image = $unique_name;
      }

      $question->title        = request("ar_question_title");
      $question->description  = request("ar_question_desc");
      $question->is_skippable = request("is_skippable") == "true" ? 1 : 0;

      $question->answers()->update([
        "score" => request("score"),
        "text" => request("answer_equation"),
        "formula" => request("answer_equation_formula"),
        "decimals" => request("answer_decimals"),
      ]);

      if (request("variables") != null && count(request("variables")) > 0) {
        $variables = [];
        foreach (request("variables") as $variable) {
          $variables[] = [
            "title" => $variable["name"],
            "decimal" => $variable["decimal"],
            "min_range" => $variable["min"],
            "max_range" => $variable["max"],
          ];
        }
        $question->equationVariables()->delete();
        $question->equationVariables()->createMany($variables);
      }
    }

    return $question->save();
  }

  public function image_actions(Question $question, Request $request) {

    $request->validate([
      "action" => "required|in:remove",
    ]);

    if (auth()->user()->is_admin() || auth()->user()->can_manage_course(auth()->user()->id, $question->test->course_id)) {

      if (request("action") == "remove") {
        if ($question->image != null && File::exists(public_path("images/uploads/$question->image"))) {
          File::delete(public_path("images/uploads/$question->image"));
        }
        $question->image = null;
      }

      return $question->save();
    }
    return false;
  }

  public function reorder(Request $request) {

    $request->validate([
      "questions" => "required|array|min:2",
      "questions.*.id" => "required|integer|exists:questions",
      "questions.*.order" => "required|integer|min:1",
    ]);

    $orders = [];
    foreach (request("questions") as $question_data) {
      $orders[$question_data["id"]] = $question_data["order"];
      // $res = Question::where('id', )->update(['order' => $question_data["order"]]);
    }
    $res = Question::updateValues($orders);

    return ["status" => boolval($res)];
  }

  public function destroy(Question $question) {
    if ($question->image != NULL && File::exists(public_path("images/questions/$question->image"))) {
      File::delete(public_path("images/questions/$question->image"));
    }
    if (!empty($question->answers) && count($question->answers) > 0) {
      foreach ($question->answers as $answer) {
        if ($answer->image != NULL && File::exists(public_path("images/questions/$answer->image"))) {
          File::delete(public_path("images/questions/$answer->image"));
        }
      }
    }
    return Question::where("id", "=", $question->id)->delete();
  }
}
