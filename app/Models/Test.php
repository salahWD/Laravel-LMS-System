<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TestAttempt;
use App\Models\Certificate;
use App\Models\CourseItem;
use App\Models\Question;
use App\Models\Result;

class Test extends CourseItem {
  use HasFactory;

  const type = 2;

  public $table = "tests";
  protected $fillable = [
    "title",
  ];

  public function certificate() {
    return $this->belongsTo(Certificate::class);
  }

  public function has_certificate() {
    if ($this->has_certificate_cash == null) {
      $this->has_certificate_cash = $this->certificate()->count() > 0;
    }
    return $this->has_certificate_cash;
  }

  public function questions() {
    return $this->hasMany(Question::class)->orderBy("order", "ASC");
  }

  public function questions_with_answers() {
    return $this->hasMany(Question::class)->with("answers");
  }

  public function questions_reverse() {
    return $this->hasMany(Question::class)->orderBy("order", "DESC");
  }

  public function result() {
    return $this->hasOne(Result::class);
  }

  public function courseItem() {
    return $this->morphOne(CourseItem::class, 'itemable');
  }

  public function testAttempts() {
    return $this->hasMany(TestAttempt::class)->orderBy("created_at", "DESC");
  }

  public function unique_attempts_count() {
    return $this->hasMany(TestAttempt::class)->select('user_id')->groupBy("user_id")->get()->count();
  }

  // public function unique_attempts_count() {
  //   return $this->hasMany(TestAttempt::class)->selectRaw("user_id")->groupBy("user_id");
  // }

  public function has_intro() {
    return $this->has_intro == 1 && !empty($this->title);
  }

  public function has_result() {
    if ($this->has_result_cash != null) {
      return $this->has_result_cash;
    } else {
      $this->has_result_cash = $this->result()->count() > 0;
      return $this->result()->count() > 0;
    }
  }

  public function has_intro_image() {
    return $this->intro_image != null && !empty($this->intro_image);
  }

  public function intro_image_url() {
    if ($this->intro_image) {
      return url("images/tests/" . $this->intro_image);
    } else {
      return null;
    }
  }

  public function image_url() {
    if ($this->intro_image != null) {
      return url("images/tests/" . $this->intro_image);
    } else {
      return url("images/tests/thumbnail.svg");
    }
  }

  public function totalScore() {
    if ($this->total_score != null) {
      return $this->total_score;
    } else {
      if ($this->questions_with_answers) {
        $total = 0;
        foreach ($this->questions_with_answers as $question) {
          if ($question->is_multi_select == 1) {
            if ($question->answers) {
              foreach ($question->answers as $answer) {
                if ($answer->score > 0) {
                  $total += $answer->score;
                }
              }
            }
          } else {
            $largestScore = 0;
            if ($question->answers) {
              foreach ($question->answers as $answer) {
                if ($answer->score > $largestScore) {
                  $largestScore = $answer->score;
                }
              }
            }
            $total += $largestScore;
          }
        }
        $this->total_score = $total;
        return $total;
      }
      return 0;
    }
  }

  public function next_item() {
    if ($this->courseItem && $this->courseItem->course_id != null) {
      if ($this->next_item_cash) {
        return $this->next_item_cash;
      } else {
        $res = \DB::table("course_items")->where('order', '>', $this->courseItem->order)
          ->where("course_id", "=", $this->courseItem->course_id)
          ->orderBy("order")->first();
        $res = new CourseItem((array)$res);
        if ($res->id) {
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
}
