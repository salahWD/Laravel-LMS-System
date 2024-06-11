<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Report;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Article;
// use Laravel\Cashier\Billable;

class User extends Authenticatable {
  // use Billable; // cashier
  use HasApiTokens, HasFactory, Notifiable;

  protected $hidden = [
    'password',
    'remember_token',
  ];

  protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
  ];

  protected $fillable = [
    'username',
    'email',
    'password',
    'permission',
  ];

  public function __construct() {
    $this->username = null;
    $this->email = null;
    $this->image = null;
    $this->first_name = null;
    $this->last_name = null;
    $this->permission = 1;
  }

  public function reports() {
    return $this->hasManyThrough(Report::class, Comment::class);
  }

  public function comments() {
    return $this->hasMany(Comment::class);
  }

  public function articles() {
    return $this->hasMany(Article::class);
  }

  public function courses() {
    return $this->hasMany(Course::class);
  }

  public function created_certificates() {
    return $this->hasMany(Certificate::class);
  }

  public function have_created_certificate($certificate_id) { // boolean
    return Certificate::where("id", $certificate_id)->where("user_id", $this->id)->count() > 0;
  }

  public function certificates() {
    return $this->belongsToMany(Certificate::class, "certificate_user", "user_id", "certificate_id");
  }

  public function studiable_courses() {
    return $this->belongsToMany(Course::class, "course_user", "student_id", "course_id");
  }

  public function watched_items($course_id = null) {
    if ($course_id != null) {
      return $this->belongsToMany(CourseItem::class, "item_user", "student_id", "course_item_id")->where("course_id", "=", $course_id);
    } else {
      return $this->belongsToMany(CourseItem::class, "item_user", "student_id", "course_item_id");
    }
  }

  public function created_tests() {
    return $this->hasManyThrough(CourseItem::class, Course::class)
      ->join("tests", "tests.id", "=", "course_items.itemable_id")
      ->where("course_items.itemable_type", "LIKE", "%Test%");
  }

  public function can_manage_course($user_id, $course_id) {
    if ($this->permission == 3) {
      // if ($user_id == 3) {
      //   return true;
      // }
      return true;
    }
    return false;
  }

  public function is_teacher() {
    return $this->permission == 3;
  }

  public function is_moderator() {
    return $this->permission == 2;
  }

  public function status_class() {
    // if ($this->permission == -1) {
    //   return "info";
    // }
    if ($this->permission <= 3 && $this->permission >= 0) {
      return ['danger', 'success', 'primary', 'dark'][$this->permission];
    }
    return "danger";
  }

  public function status_text() {
    if ($this->permission == 3) {
      return __("Admin");
    } elseif ($this->permission == 2) {
      return __("Moderator");
    } elseif ($this->permission == 1) {
      return __("Active");
    } elseif ($this->permission == 0) {
      return __("Disabled");
      // } elseif ($this->permission == -1) {
      //   return __("New User");
    } else {
      return __("error in this user");
    }
  }

  public function get_permissions() {
    return [
      (object)["id" => 0, "name" => "Inactive"],
      (object)["id" => 1, "name" => "Active"],
      (object)["id" => 2, "name" => "Moderator"],
      (object)["id" => 3, "name" => "Admin"],
    ];
  }

  public function is_admin() {
    return $this->permission == 3;
  }

  public function fullname() {
    if (!($this->first_name || $this->last_name)) {
      return $this->username;
    }
    return $this->first_name . " " . $this->last_name;
  }

  public function image_url() {
    if ($this->image) {
      return url("images/users/$this->image");
    } else {
      return url("images/users/placeholder-user.jpg");
    }
  }

  public function can_report() {
    return $this->permission > 1;
  }

  public function is_enrolled_in($course_id) {
    return $this->studiable_courses()->where("course_id", $course_id)->count() > 0;
  }

  public function last_watched_of_course($course_id) {

    $last_watched_item = null;
    $all_items = Course::where("courses.id", $course_id)
      // ->where("course_items.status", 1)
      ->join("course_items", "courses.id", "=", "course_items.course_id")
      ->select("course_items.id")
      ->pluck("course_items.id")
      ->toArray();
    $watched_items = auth()->user()->watched_items()->select("course_items.id")->pluck("course_items.id")->toArray();

    if (count($all_items) > 0) {
      if (count($watched_items) > 0) {
        $trig = true;
        foreach ($all_items as $i => $item) {
          if (in_array($item, $watched_items)) {
            $all_items[$i] = ["is_open" => true, "id" => $item];
          } else {
            if ($trig) {
              if (
                isset($all_items[$i - 1]) &&
                is_array($all_items[$i - 1]) &&
                $all_items[$i - 1]["is_open"] != null &&
                $all_items[$i - 1]["is_open"]
              ) {
                $last_watched_item = $item;
              }
              $trig = false;
            }
          }
        }
        if ($last_watched_item) {
          return $last_watched_item;
        }
      } else {
        return $all_items[0];
      }
    }
    return null;
  }

  public function has_certificate($certificate_id) {
    return $this->certificates()->where("certificate_id", $certificate_id)->count() > 0;
  }

  public function can_enroll_course() {
    return $this->permission > 0;
  }
}
