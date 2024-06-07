<?php

namespace App\Http\Controllers;

use App\Jobs\LoadLectures;
use File;
use App\Models\Course;
use App\Models\CourseItem;
use App\Models\Lecture;
use Illuminate\Http\Request;

class CourseController extends Controller {

  public function enroll(Request $request, Course $course) {
    if (auth()->user()->can_enroll_course()) {
      if ($course->is_free()) {
        $course->students()->syncWithoutDetaching([auth()->user()->id]);
        $request->session()->flash('course_enrolled', true);
      } else {
        dd("payment methods are not ready yet");
      }
    }
    return redirect()->route("course_show", $course->id);
  }

  public function create() {
    if (auth()->user()->is_teacher() || auth()->user()->is_admin()) {
      $course = new Course;
      return view('dashboard.course')->with("course", $course);
    }
  }

  public function store(Request $request) {
    if (auth()->user()->is_teacher() || auth()->user()->is_admin()) {

      $request->validate([
        "title" => "required|string|max:120",
        "description" => "nullable|string",
        "price" => "nullable|integer|min:0",
        "order" => "nullable|integer|min:0|max:500",
        "ytlink" => "nullable|url:https",
        "status" => "required|integer|min:0|max:1",
        "image" => "nullable|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte
      ]);

      $info = [
        "title" => request("title"),
        "user_id" => auth()->user()->id,
        "description" => request("description"),
        "price" => request("price"),
        "status" => request("status") == 1 ? 1 : 0,
      ];

      if (request('order') != null) {
        $info["order"] = request("order");
      }

      if ($request->hasFile('image')) {
        // upload image
        $info["image"] = date('mdYHis') . uniqid() . substr($request->file('image')->getClientOriginalName(), -10);
        $request->image->move(public_path('images/courses'), $info["image"]);
      }

      $course = Course::create($info);

      if (request("ytlink") != null) {
        LoadLectures::dispatch($course, request("ytlink"));
      }

      $request->session()->flash('course-saved', boolval($course->id));

      return redirect()->route("course_edit", $course->id);
    } else {
      return abort(404);
    }
  }

  public function show(Course $course) {

    $items = $course->items_with_data;
    $is_enrolled = auth()->user()->is_enrolled_in($course->id);

    if ($is_enrolled) {
      $watched_items = auth()->user()->watched_items($course->id)->get()->toArray();

      $trig = true;
      foreach ($items as $i => $item) {
        $item->is_open = false;
        $item->is_watched = false;
        // this case for first item and no watched history
        if (
          $i == 0 &&
          count(array_intersect(
            array_column($watched_items, "id"),
            $items->pluck("id")->toArray()
          )) == 0
        ) {
          $item->is_open = true;
          $item->is_last_watched = true;
          $trig = false;
        } else if ($i == 0) {
          $item->is_open = true;
        }
        if (in_array($item->id, array_column($watched_items, "id"))) {
          $item->is_open = $trig;
          $item->is_watched = true;
        } else {
          if ($trig) {
            if (isset($items[$i - 1]) && $items[$i - 1]->is_open) {
              $item->is_open = true;
              $item->is_last_watched = true;
            }
            $trig = false;
          }
        }
      }
    }
    return view("course", compact("course", "items", "is_enrolled"));
  }

  public function show_all(Request $request) {
    $courses = Course::orderBy("created_at", "DESC")->paginate(15);
    return view("courses", compact("courses"));
  }

  public function reorder_items(Request $request) {
    if (auth()->user()->can_manage_course(auth()->user()->id, request("course_id"))) {

      $request->validate([
        "items" => "required|array|min:2",
        "items.*.id" => "required|integer|exists:course_items",
        "items.*.order" => "required|integer|min:1",
      ]);

      $values = [];
      foreach (request("items") as $item) {
        $values[$item["id"]] = $item["order"];
      }
      $res = CourseItem::updateValues($values);
      // dd($res);
      return ["result" => boolval($res)];
    } else {
      return abort(404);
    }
  }

  public function edit(Course $course) {
    if (auth()->user()->is_admin() || auth()->user()->id == $course->user_id) {
      return view('dashboard.course')->with("course", $course);
    } else {
      return abort(404);
    }
  }

  public function update(Request $request, Course $course) {
    if (auth()->user()->id == $course->user_id || auth()->user()->is_admin()) {

      $request->validate([
        "title" => "required|string|max:120",
        "description" => "nullable|string",
        "order" => "nullable|integer|min:0|max:500",
        "price" => "nullable|integer|min:0",
        "status" => "required|integer|min:0|max:1",
        "image" => "nullable|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte
      ]);

      $course->title = request("title");
      $course->status = request("status") == 1 ? 1 : 0;

      if (request("description") != null) {
        $course->description = request("description");
      }

      if (request("price") != null) {
        $course->price = request("price");
      }

      if (request("order") != null) {
        $course->order = request("order");
      }

      if ($request->hasFile('image')) {
        // delete old image
        if ($course->image != NULL && File::exists(public_path("images/courses/$course->image"))) {
          File::delete(public_path("images/courses/$course->image"));
        }
        // upload new image
        $course->image = date('mdYHis') . uniqid() . substr($request->file('image')->getClientOriginalName(), -10);
        $request->image->move(public_path('images/courses'), $course->image);
      }

      $res = $course->save();
      $request->session()->flash('course-saved', $res);

      return redirect()->route("course_edit", $course->id);
    } else {
      return abort(404);
    }
  }

  public function destroy(Course $course) {

    if (auth()->user()->id == $course->user_id || auth()->user()->is_admin()) {

      if ($course->items->count() > 0) {

        foreach ($course->items as $item) {

          if ($item->is_lecture()) {
            // delete old image
            if ($item->thumbnail != NULL && File::exists(public_path("lectures/thumbnails/$item->thumbnail"))) {
              File::delete(public_path("lectures/thumbnails/$item->thumbnail"));
            }
            // delete old videos
            if ($item->video != NULL && File::exists(public_path("lectures/videos/$item->video"))) {
              File::delete(public_path("lectures/videos/$item->video"));
            }
          }

          if ($item->is_test()) {
            // delete old image
            if ($item->thumbnail != NULL && File::exists(public_path("images/tests/" . $item->thumbnail))) {
              File::delete(public_path("images/tests/" . $item->thumbnail));
            }
            // delete old image
            if ($item->intro_image != NULL && File::exists(public_path("images/tests/" . $item->intro_image))) {
              File::delete(public_path("images/tests/" . $item->intro_image));
            }
          }

          $item->itemable()->delete();
          $item->delete();
        }
      }

      $course->delete();

      return redirect()->route("courses_manage");
    } else {
      return abort(404);
    }
  }
}
