<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use App\Models\CourseItem;
use Google\Client;
use Google\Service\YouTube;
use Throwable;

class Lecture extends CourseItem {
  use HasFactory;

  const type = 1;

  public $table = "lectures";
  public $duration;

  public $fillable = [
    "title",
    "description",
    "status",
    "order",
    "thumbnail",
    "video",
    "user_id",
  ];

  public function courseItem() {
    return $this->morphOne(CourseItem::class, 'itemable');
  }

  public function has_course() {
    $course = $this?->courseItem?->course;
    return $course != null;
  }

  public function course() {
    return $this->courseItem->course();
  }

  public function video_url() {
    if ($this->is_yt_video()) {
      return $this->video;
    } else {
      return url("lectures/videos/" . $this->video);
    }
  }

  public function is_yt_video() {
    return Str::isUrl($this->video) && Str::contains($this->video, ["youtube.com"]);
  }

  public function yt_link_id() {
    if ($this->is_yt_video()) {
      $url = explode("/", $this->video);
      return end($url);
    }
    return null;
  }

  public function yt_link() {
    if ($this->is_yt_video()) {
      $url = explode("/", $this->video);
      return "https://youtube.com/watch?v=" . end($url);
    }
    return null;
  }

  public function duration() {
    if ($this->duration) {
      return $this->duration;
    }
    $getID3 = new \getID3;
    if (\File::exists(public_path("lectures/videos/" . $this->video))) {
      $file = $getID3->analyze(public_path("lectures/videos/" . $this->video));
      if ($file['playtime_seconds'] > 60 * 60 * 1000) {
        $this->duration = date("H:i:s", $file['playtime_seconds']);
        return date("H:i:s", $file['playtime_seconds']);
      } else {
        $this->duration = date('i:s', $file['playtime_seconds']);
        return date('i:s', $file['playtime_seconds']);
      }
    }
  }

  public function image_url() {
    if ($this->thumbnail != null) {
      return url("lectures/thumbnails/" . $this->thumbnail);
    } else {
      return url("lectures/thumbnails/thumbnail.svg");
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

  public static function parse_yt_thumbnail($video_id, $download_image = true) {
    $apiKey = config('app.yt_api_key');

    $client = new Client();
    $client->setDeveloperKey($apiKey);
    $youtube = new YouTube($client);

    try {
      // Call the YouTube API to retrieve video details
      $video = $youtube->videos->listVideos('snippet', ['id' => $video_id]);

      // Extract the best available thumbnail URL
      $thumbnails = $video[0]['snippet']['thumbnails'];
      $thumbnail_url = '';
      foreach ($thumbnails as $size => $thumbnail) {
        if ($size == 'maxres') {
          $thumbnail_url = $thumbnail['url'];
          break;
        } elseif ($size == 'standard') {
          $thumbnail_url = $thumbnail['url'];
        } elseif ($size == 'high') {
          $thumbnail_url = $thumbnail['url'];
        } elseif ($size == 'medium') {
          $thumbnail_url = $thumbnail['url'];
        } elseif ($size == 'default') {
          $thumbnail_url = $thumbnail['url'];
        }
      }

      if ($download_image) {

        $image_name = date('mdYHis') . uniqid() . substr($thumbnail_url, -10);

        // ======== get file from link ========
        $ch = curl_init($thumbnail_url);
        $fp = fopen(public_path('/lectures/thumbnails/' . $image_name), 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        return $image_name;
      } else {
        return $thumbnail_url;
      }
    } catch (Google_Service_Exception $e) {
      // Handle exceptions
      return false;
    }
    return false;
  }
}
