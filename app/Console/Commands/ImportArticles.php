<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\Article;

class ImportArticles extends Command {
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'import:articles {path}';


  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Import articles from an SQL file without inserting it into the old database';

  /**
   * Execute the console command.
   */
  public function handle() {
    $count = 0;
    $path = $this->argument('path');

    // Check if the file exists
    if (!File::exists($path)) {
      $this->error("File not found: $path");
      return;
    }

    // Read the SQL file
    $sql = File::get($path);

    // Extract the insert statements
    preg_match_all('/INSERT INTO `et_posts` .*?VALUES\n(.*?);/is', $sql, $matches);

    $titles_arr = Article::select("title")->join("article_translations", "article_id", "=", "articles.id")->get()->pluck("title")->toArray();
    foreach ($matches[1] as $i => $insertStatement) {
      if ($i != 0) {

        preg_match_all('/\((.*?)[0-9]?\),[\r\n]+/is', $insertStatement, $rows);
        // Extract values from the insert statement
        foreach ($rows[1] as $row) {
          $columns = str_getcsv($row, ',', "'");

          $status = $columns[7];
          if ($status == "publish") {
            $postContent = $columns[4];
            $postTitle = $columns[5];

            // Clean the content
            $cleanedContent = $this->cleanContent($postContent);

            preg_match_all('/\<img.*?src\=["\']?(.*?)["\'].*?\/?\>/is', $cleanedContent, $thumbnail);

            if ($thumbnail) {
              $thumbnail = end($thumbnail[1]);
              $thumbnail = explode("images/articles/", $thumbnail);
              $thumbnail = end($thumbnail);
            }

            // Insert into the new database
            if (!in_array($postTitle, $titles_arr)) {
              $art = Article::create([
                'user_id' => 1,
                "ar" => [
                  'title' => $postTitle,
                  'content' => $cleanedContent,
                ],
                'image' => isset($thumbnail) && is_string($thumbnail) ? $thumbnail : null,
                'status' => 2,
                'created_at' => now(),
                'updated_at' => now(),
              ]);
              $count++;

              $this->info("[$count] Article Import Succeed ( ID: " . $columns[0] . ", Title: $postTitle)");
            } else {
              $this->info("old article ( ID: " . $columns[0] . ", Title: $postTitle)");
            }
          }
        }
      }
    }

    $this->info('All articles imported successfully!');
  }

  private function cleanContent($content) {
    // Remove specific escape sequences
    $content = str_replace(['\l', '\n', '\r', '\u'], '', $content);

    $content = preg_replace(['/\[button.*?\]/', '/\[\/button\]/'], ['<button class="large-btn">', '</button>'], $content);

    // [Youtube_Channel_Gallery feed=\"playlist\" user=\"PLhZYrO_t1vLc2Yfhg_UhPIMjOwTbeXJgJ\" videowidth=\"580\" ratio=\"16x9\" theme=\"light\" color=\"white\" autoplay=\"0\" rel=\"0\" showinfo=\"0\" maxitems=\"999\" thumb_width=\"277\" thumb_ratio=\"16x9\" thumb_columns_ld=\"4\" title=\"1\" description=\"0\" promotion=\"0\" pagination_show=\"0\" description_words_number=\"6\" link=\"1\"  key=\"AIzaSyDrS-J0bZgY4m_P93Jr36t3hwAsPP6Ht-c\"]
    $content = preg_replace('/\[Youtube_Channel_Gallery.*?\]/', '<h1>youtube channel gallery here</h1>', $content);

    // [youtube http://www.youtube.com/watch?v=ITGZFJ9cXL8]
    preg_match_all('/\[youtube .*\?v\=(.*)\]/is', $content, $youtubeVideo);
    if (!empty($youtubeVideo) && isset($youtubeVideo[1]) && !empty($youtubeVideo[1])) {
      $content = preg_replace(
        '/\[youtube .*\]/is',
        '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $youtubeVideo[1][0] . '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
        $content
      );
    }

    // Remove shortcodes
    $content = preg_replace('/\[.*?\]/', '', $content);

    // Remove escaping attributes with "
    $content = str_replace(['\\"', '\\\''], ['"', '\''], $content);

    $possible_images = [
      'http://www.eletorial.com/wp-content/' => "/images/articles/",
      'http://eletorial.com/wp-content/' => "/images/articles/",
    ];

    $content = str_replace(array_keys($possible_images), array_values($possible_images), $content);

    // Convert HTML entities
    $content = html_entity_decode($content, ENT_QUOTES | ENT_XML1, 'UTF-8');

    // Clean up line breaks and spaces
    $content = preg_replace('/\s+/', ' ', $content);

    // Trim any leading/trailing whitespace
    $content = trim($content);

    return $content;
  }
}
