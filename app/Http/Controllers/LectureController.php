<?php

namespace App\Http\Controllers;

use App\Models\Lecture;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseItem;

class LectureController extends Controller {

  public function index() {
  }

  public function create() {
    $lecture = new Lecture();
    $courses = Course::orderBy("created_at", "DESC")->get();
    return view("dashboard.lecture", compact("lecture", "courses"));
  }

  public function create_for(Course $course) {
    $lecture = new Lecture();
    $courses = Course::orderBy("created_at", "DESC")->get();
    return view("dashboard.lecture")->with([
      "lecture" => $lecture,
      "selected_course" => $course,
      "courses" => $courses,
    ]);
  }

  public function store(Request $request) {
    $request->validate([
      "title" => "required|string",
      "description" => "nullable|string",
      "course" => "required|exists:courses,id",
      // "order" => "nullable|integer|max:255",
      "status" => "required|integer|min:0|max:1",
      "image" => "nullable|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte
      "video" => "required_if:ytlink,null|file|mimetypes:video/mp4",
      "ytlink" => [
        "required_if:video,null", "url:https",
        function ($attribute, $value, $fail) {
          if (!Str::isUrl($value) || !Str::contains($value, ["youtube.com"])) {
            $fail("The {$attribute} attibute is not a valid youtube link.");
          }
          parse_str(parse_url($value, PHP_URL_QUERY), $url);
          if (empty($url) || !is_array($url) || !isset($url["v"])) {
            $fail("The {$attribute} does not have video id [?v=video_id].");
          }
        }
      ]
    ]);
    if (auth()->user()->can_manage_course(request("user_id"), request("course"))) {
      $info = [
        "title" => request("title"),
        "description" => request("description"),
        // "course_id" => request("course"),
        "user_id" => auth()->user()->id,
        // "order" => CourseItem::generate_order(request("course")),
        "status" => request("status"),
      ];
      if (request("ytlink") != null) {
        parse_str(parse_url(request("ytlink"), PHP_URL_QUERY), $result);
        $video_id = $result["v"];
        if ($video_id) {
          $info["video"] = "https://www.youtube.com/embed/" . $video_id;
          if (!$request->hasFile('image')) {
            $info["thumbnail"] = Lecture::parse_yt_thumbnail($video_id);
          }
        }
      } elseif ($request->hasFile('video')) {
        // upload video
        $info["video"] = date('mdYHis') . uniqid() . substr($request->file('video')->getClientOriginalName(), -10);
        $request->video->move(public_path('lectures/videos'), $info["video"]);
      }
      if ($request->hasFile('image')) {
        // upload image
        $info["thumbnail"] = date('mdYHis') . uniqid() . substr($request->file('image')->getClientOriginalName(), -10);
        $request->image->move(public_path('lectures/thumbnails'), $info["thumbnail"]);
      }

      $lecture = Lecture::create($info);
      $lecture->courseItem()->create([
        "course_id" => request("course"),
        "order" => CourseItem::generate_order(request("course")),
      ]);

      $request->session()->flash('lecture-saved', boolval($lecture->id));

      return redirect()->route("course_edit", [request("course"), "#lectures"]);
    }
  }

  public function show(Lecture $lecture) {
    if ($lecture->open_for_user(auth()->user()->id)) {

      $all_items = $lecture?->courseItem?->course?->items_with_data;
      $watched_items = auth()->user()->watched_items()->select("course_items.id")->pluck("course_items.id")->toArray();

      $getID3 = new \getID3;

      $trig = true;
      $items = [];
      if ($all_items) {
        foreach ($all_items as $i => $item) {
          $item->is_open = false;

          if ($item->is_lecture()) {
            if (!$item->itemable->is_yt_video() && \File::exists(public_path("lectures/videos/" . $item->video))) {
              $file = $getID3->analyze(public_path("lectures/videos/" . $item->video));
              if ($file['playtime_seconds'] > 60 * 60 * 1000) {
                $item->duration = date("H:i:s", $file['playtime_seconds']);
              } else {
                $item->duration = date('i:s', $file['playtime_seconds']);
              }
            } else {
              $item->duration = null;
            }
          }
          if ($item->id == $lecture->id) {
            $item->is_current = true;
          }
          if (in_array($item->id, $watched_items)) {
            $item->is_open = true;
          } else {
            if ($trig) {
              if (isset($all_items[$i - 1]) && $all_items[$i - 1]->is_open) {
                $item->is_open = true;
                $item->is_last_watched = true;
              }
              $trig = false;
            }
          }
          $items[] = $item;
        }
      }

      return view("lecture")->with([
        "current_lecture" => $lecture,
        "all_items" => $items,
      ]);
    } else {
      abort(404);
    }
  }

  public function edit(Lecture $lecture) {
    return view("dashboard.lecture", compact("lecture"));
  }

  public function update(Request $request, Lecture $lecture) {
    $request->validate([
      "title" => "required|string",
      "description" => "nullable|string",
      // "order" => "nullable|integer|max:255",
      "status" => "required|integer|min:0|max:1",
      "image" => "nullable|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte
      "video" => "nullable|file|mimetypes:video/mp4",
      "ytlink" => [
        "required_if:video,null", "url:https",
        function ($attribute, $value, $fail) {
          if (!Str::isUrl($value) || !Str::contains($value, ["youtube.com"])) {
            $fail("The {$attribute} attibute is not a valid youtube link.");
          }
          parse_str(parse_url($value, PHP_URL_QUERY), $url);
          if (empty($url) || !is_array($url) || !isset($url["v"])) {
            $fail("The {$attribute} does not have video id [?v=video_id].");
          }
        }
      ]
    ]);
    if (auth()->user()->can_manage_course(request("user_id"), request("course_id"))) {
      $lecture->title = request("title");
      // $lecture->order = request("order");
      $lecture->status = request("status");

      if (request("description") != null) {
        $lecture->description = request("description");
      }
      if (request("order") != null) {
        $lecture->order = request("order");
      }

      if (request("ytlink") != null) {
        parse_str(parse_url(request("ytlink"), PHP_URL_QUERY), $result);
        $video_id = $result["v"];
        if ($video_id) {
          $lecture->video = "https://www.youtube.com/embed/" . $video_id;
          if (!$request->hasFile('image')) {
            $lecture->thumbnail = Lecture::parse_yt_thumbnail($video_id);
          }
        }
      } elseif ($request->hasFile('video')) {
        // delete old video
        if ($lecture->video != NULL && File::exists(public_path("lectures/videos/$lecture->video"))) {
          File::delete(public_path("lectures/videos/$lecture->video"));
        }
        // upload video
        $lecture->video = date('mdYHis') . uniqid() . substr($request->file('video')->getClientOriginalName(), -10);
        $request->video->move(public_path('lectures/videos'), $lecture->video);
      }
      if ($request->hasFile('image')) {
        // delete old thumbnail
        if ($lecture->thumbnail != NULL && File::exists(public_path("lectures/thumbnails/$lecture->thumbnail"))) {
          File::delete(public_path("lectures/thumbnails/$lecture->thumbnail"));
        }
        // upload image
        $lecture->thumbnail = date('mdYHis') . uniqid() . substr($request->file('image')->getClientOriginalName(), -10);
        $request->image->move(public_path('lectures/thumbnails'), $lecture->thumbnail);
      }

      $res = $lecture->save();

      $request->session()->flash('lecture-saved', $res);

      return redirect()->route("lecture_edit", $lecture->id);
    }
  }

  public function api_destroy(Request $request, Lecture $lecture) {
    $request->validate([
      "page" => "integer:min:0",
    ]);

    $per_page = 15; // lecture pagination
    $next_lecture = Lecture::orderBy("created_at", "DESC")->skip($per_page * request("page"))->first();
    $res = $lecture->delete();

    if ($next_lecture != null) {
      $next_lecture = [
        "id" => $next_lecture->id,
        "title" => $next_lecture->title,
        "description" => $next_lecture->description,
        "order" => $next_lecture->order,
        "status" => $next_lecture->image_url(),
        "category_title" => $next_lecture?->category?->title,
        "status_icon" => $next_lecture->status_icon(),
        "image" => $next_lecture->image_url(),
        "date" => $next_lecture->get_date(),
      ];
    }

    return ["result" => $res, "next_lecture" => $next_lecture];
  }

  public function ajax_done(Request $request, Lecture $lecture) {
    if ($lecture->open_for_user(auth()->user()->id)) {
      // $res = $lecture->watched_by()->attach(auth()->user()->id);
      auth()->user()->watched_items()->attach($lecture->courseItem->id);
      return ["result" => true];
    }
    abort(404);
  }

  public function destroy(Lecture $lecture) {
  }
}
