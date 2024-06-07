<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Lecture;
use App\Models\Test;
use App\Models\CourseItem;

class Course extends Model {
  use HasFactory;

  private $total_duration_prop;

  public $fillable = [
    "user_id",
    "price",
    "title",
    "order",
    "status",
    "description",
    "image",
  ];

  public function user() {
    return $this->belongsTo(User::class);
  }

  // public function lectures() {
  //   return $this->hasManyThrough(Lecture::class, CourseItem::class)->orderBy("order", "ASC")->orderBy("created_at", "DESC");
  // }

  // public function tests() {
  //   return $this->hasMany(Test::class)->orderBy("order", "ASC")->orderBy("created_at", "DESC");
  // }

  public function items() {
    return $this->hasMany(CourseItem::class)->orderBy("order");
  }

  public function items_with_data() {
    return $this->hasMany(CourseItem::class)->with("itemable")->orderBy("order");
  }

  public function has_items() {
    return $this->items()->count() > 0;
  }

  public function students() {
    return $this->belongsToMany(User::class, "course_user", "course_id", "student_id");
  }

  // public function price() {
  //   if ($this->price != null && $this->price > 0) {
  //     return $this->price;
  //   } else {
  //     return 0;
  //   }
  // }

  public function is_free() {
    return !($this->price != null && $this->price > 0);
  }

  public function show_price() {
    if ($this->price != null && $this->price > 0) {
      return '$' . $this->price;
    } else {
      return __("free");
    }
  }

  public function total_time() {
    if ($this->total_duration_prop) { // total_duration_prop => cash duration in the model
      return $this->total_duration_prop;
    } else {

      $all_videos = \DB::table("lectures")
        ->join("course_items", "itemable_id", "=", "lectures.id")
        ->where("itemable_type", "App\Models\Lecture")
        ->where("course_items.course_id", $this->id)
        ->where("status", 1)
        ->select("video")
        ->get();

      if ($all_videos) {
        $getID3 = new \getID3;
        $total_time = 0;
        foreach ($all_videos as $lect) {
          if (\File::exists(public_path("lectures/videos/" . $lect->video))) {
            $file = $getID3->analyze(public_path("lectures/videos/" . $lect->video));
            $total_time += $file['playtime_seconds'];
          }
        }
        if ($total_time > 60 * 60 * 1000) {
          $this->total_duration_prop = date("H:i:s", $total_time);
          return date("H:i:s", $total_time);
        } else {
          $this->total_duration_prop = date("i:s", $total_time);
          return date('i:s', $total_time);
        }
      } else {
        return "00:00";
      }
    }
  }

  public function image_url() {
    if ($this->image) {
      return url("images/courses/$this->image");
    } else {
      return url("images/courses/default-course.svg");
    }
  }
}
