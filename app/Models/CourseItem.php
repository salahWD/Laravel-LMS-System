<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CourseItem extends Model {
  use HasFactory;

  public $timestamps = false;

  public $table = "course_items";

  protected $fillable = [
    "id",
    "course_id",
    "itemable_id",
    "itemable_type",
    "order",
  ];

  public function course() {
    return $this->belongsTo(Course::class);
  }

  public function open_for_user($user_id) {
    if (auth()->user()->is_admin()) {
      return true;
    }
    if ($this->courseItem->order == 1) {
      return true;
    } else {
      $res = DB::table("course_items")
        ->join("item_user", "course_items.id", "=", "item_user.course_item_id")
        ->where("student_id", $user_id)
        ->where("course_id", $this->courseItem->course_id)
        ->where("course_items.order", $this->courseItem->order - 1)
        ->orWhere("course_items.order", $this->courseItem->order)
        ->count();
      if ($res > 0) {
        return true;
      }
      return false;
    }
  }

  // public static function generate_order_for($course_id) {
  //   $order = Lecture::where("course_id", $course_id)->selectRaw("COUNT(lectures.id) + 1 AS `order`")->get()->first();
  //   if ($order->order != null) {
  //     return $order->order;
  //   } else {
  //     return 1;
  //   }
  // }

  public static function generate_order($course_id) {
    $order = CourseItem::where("course_id", $course_id)->max("order");
    return $order != null ? $order + 1 : 1;
  }

  public function is_lecture() {
    return $this->itemable_type == "App\Models\Lecture";
  }

  public function is_test() {
    return $this->itemable_type == "App\Models\Test";
  }

  public function itemable() {
    return $this->morphTo();
  }

  public function next_item() {
    if ($this->courseItem && $this->courseItem->course_id != null) {
      if ($this->next_item_cash) {
        return $this->next_item_cash;
      } else {
        $res = CourseItem::where('order', '>', $this->courseItem->order)
          ->where("course_id", "=", $this->courseItem->course_id)
          ->orderBy("order")->toSql();
        dd($res);
        if ($res?->id) {
          $this->next_item_cash = $res;
          return $res;
        } else {
          return null;
        }
      }
    } else {
      return null;
    }
  }

  public function status_icon() {
    if ($this->status >= 0 && $this->status <= 2) {
      $icons = [
        '<svg class="icon icon-xs me-2 text-primary" data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"></path>
        </svg>',
        '<svg class="icon icon-xs me-2 text-primary" data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418"></path>
        </svg>',
      ];
      return $icons[$this->status];
    } else {
      return "error in icon";
    }
  }

  public function get_date() {
    $date = Carbon::parse($this->created_at);
    if ($date != null) {
      return $date->format("Y/m/d g:i A");
    } else {
      return $this->created_at;
    }
  }

  /* db query builder function */
  public static function updateValues(array $values) {
    $table = CourseItem::getModel()->getTable();

    $cases = [];
    $ids = [];
    $params = [];

    foreach ($values as $id => $value) {
      $id = (int)$id;
      $cases[] = "WHEN {$id} then ?";
      $params[] = $value;
      $ids[] = $id;
    }

    $ids = implode(',', $ids);
    $cases = implode(' ', $cases);
    // $params[] = Carbon::now();

    return \DB::update(
      "UPDATE `{$table}` SET `order` = CASE `id` {$cases} END WHERE `id` in ({$ids})",
      $params
    );
  }
}
