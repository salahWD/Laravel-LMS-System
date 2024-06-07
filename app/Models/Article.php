<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Tag;

class Article extends Model implements TranslatableContract {

  use HasFactory;
  use Translatable;

  public $translatedAttributes = ['title', 'description', 'content'];

  protected $fillable = [
    "image",
    "comment_status",
    "user_id",
    "category_id",
    "status",
  ];

  // public function __construct() {
  //   $this->image = null;
  //   $this->status = null;
  // }

  public function category() {
    return $this->belongsTo(Category::class);
  }

  public function user() {
    return $this->belongsTo(User::class);
  }

  public function tags() {
    return $this->belongsToMany(Tag::class);
  }

  public function comments() {
    return $this->hasMany(Comment::class);
  }

  public function tags_text($tags) {
    if (is_string($tags)) {
      $tags_arr = explode(",", $tags);
      $tags_arr = array_filter($tags_arr, function ($val) {
        return trim($val);
      });
      $str = '';
      foreach ($tags_arr as $tag) {
        $str .= "$tag,";
      }
      return rtrim($str, ",");
    } else if ($this->tags->count()) {
      $str = '';
      foreach ($this->tags as $tag) {
        $str .= "$tag->title,";
      }
      return rtrim($str, ",");
    }
    return "";
  }

  public function image_url() {
    if ($this->image) {
      return url("images/articles/" . $this->image);
    } else {
      return url("images/articles/article-thumbnail.svg");
    }
  }

  public function status_name() {
    if ($this->status >= 0 && $this->status <= 2) {
      return [__("private"), __("unlisted"), __("public")][$this->status];
    } else {
      return "error in status";
    }
  }

  public function status_icon() {
    if ($this->status >= 0 && $this->status <= 2) {
      $icons = [
        '<svg data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"></path>
        </svg>',
        '<svg data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"></path>
        </svg>',
        '<svg data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418"></path>
        </svg>',
      ];
      return $icons[$this->status];
    } else {
      return "error in icon";
    }
  }
}
