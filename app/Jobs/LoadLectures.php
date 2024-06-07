<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use App\Models\Course;
use App\Models\Lecture;
use App\Models\CourseItem;
use Google\Client;
use Google\Service\YouTube;
use Throwable;
use Illuminate\Support\Facades\DB;


class LoadLectures implements ShouldQueue {
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public $tries = 2;

  public function __construct(public Course $course, public $youtube_link) {
    //
  }

  public function handle(): void {

    $playlist_id = $this->youtube_link;

    if (Str::isUrl($this->youtube_link) && Str::contains($this->youtube_link, ["youtube.com"])) {
      parse_str(parse_url($this->youtube_link, PHP_URL_QUERY), $result);
      $playlist_id = $result["list"];

      if ($playlist_id) {
        $apiKey = config('app.yt_api_key');

        $client = new Client();
        $client->setDeveloperKey($apiKey);
        $service = new YouTube($client);

        $playlistItems = [];
        $pageToken = null;

        do {
          $options = [
            'playlistId' => $playlist_id,
            'maxResults' => 50, // Max results per page
            'pageToken' => $pageToken,
          ];

          $playlistItemsResponse = $service->playlistItems->listPlaylistItems('snippet', $options);
          $playlistItems = array_merge($playlistItems, $playlistItemsResponse->getItems());

          $pageToken = $playlistItemsResponse->getNextPageToken();
        } while ($pageToken);

        if (!empty($playlistItems)) {

          $videos = [];
          foreach ($playlistItems as $item) {

            $lecture_info = [
              "title" => $item->getSnippet()->getTitle(),
              "description" => $item->getSnippet()->getDescription(),
              "order" => intval($item->getSnippet()->getPosition()) + 1,
              "video" => "https://www.youtube.com/embed/" . $item->getSnippet()->getResourceId()->getVideoId(),
              "user_id" => $this->course->user_id,
            ];

            $thumbnails = $item->getSnippet()->getThumbnails();
            $biggestThumbnailSize = null;
            $biggestThumbnailUrl = null;
            foreach ($thumbnails as $size => $thumbnail) {
              if ($biggestThumbnailSize === null || $size === 'maxres') {
                $biggestThumbnailSize = $size;
                $biggestThumbnailUrl = $thumbnail->getUrl();
              }
            }
            if ($biggestThumbnailUrl == null) {
              $biggestThumbnailUrl = "no-thumbnail-picture.jpg";
            }
            $image_name = date('mdYHis') . uniqid() . substr($biggestThumbnailUrl, -10);

            // ======== get file from link ========

            /* curl method */
            $ch = curl_init($biggestThumbnailUrl);
            $fp = fopen(public_path('/lectures/thumbnails/' . $image_name), 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);

            /* copy method */
            // copy($biggestThumbnailUrl, public_path('/lectures/thumbnails/' . $image_name));

            $lecture_info["thumbnail"] = $image_name;
            $lecture_info["created_at"] = now();
            $lecture_info["updated_at"] = now();
            array_push($videos, $lecture_info);
          }

          foreach ($videos as $video) {
            $order = $video["order"];
            unset($video["order"]);
            $lecture = Lecture::create($video);
            $lecture->courseItem()->create([
              "course_id" => $this->course->id,
              "order" => $order,
            ]);
          }
        } else {
          info(json_encode($playlistItems));
          throw new \Exception("no videos");
        }
      } else {
        throw new \Exception("there is no (list) param in YT link");
      }
    } else {
      throw new \Exception("not a YT link");
    }
  }

  public function failed(Throwable $e) {
    info($e);
    info("this job has failed:");
  }
}
