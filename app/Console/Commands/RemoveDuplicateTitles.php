<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RemoveDuplicateTitles extends Command {
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'import:remove_duplicate_titles {path}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Removes articles with duplicate titles from the SQL file';

  /**
   * Execute the console command.
   */
  public function handle() {
    $path = $this->argument('path');

    // Check if the file exists
    if (!File::exists($path)) {
      $this->error("File not found: $path");
      return;
    }

    // Read the SQL file
    $sql = File::get($path);

    // Match all articles and their titles
    preg_match_all(
      "/\((\d+),\s\d+,\s'[0-9\-\s:]+',\s'[0-9\-\s:]+',\s'.*?',\s'(.*?)',.*?\),\n/im",
      $sql,
      $matches,
      PREG_SET_ORDER
    );

    $uniqueTitles = [];
    $filteredSql = $sql;

    foreach ($matches as $match) {
      $id = $match[1]; // Article ID
      $title = $match[2]; // Article title
      $fullArticle = $match[0]; // Full matched article row

      // Check if the title has already been added
      if (in_array($title, $uniqueTitles)) {
        // If it's a duplicate, remove the article from the SQL content
        $filteredSql = str_replace($fullArticle, '', $filteredSql);
      } else {
        // Otherwise, add it to unique titles
        $uniqueTitles[] = $title;
      }
    }

    // Write the filtered SQL back to the file
    $newPath = str_replace(".sql", "-filtered.sql", $path);
    File::put($newPath, $filteredSql);

    $this->info("Duplicate articles have been removed. Cleaned file saved as $newPath");
  }
}
