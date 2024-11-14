<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RemoveEmptyArticles extends Command {
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'import:remove_empty_articles {path}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Remove empty articles (with no content) from the given SQL file';

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

    // Use a regular expression to match any INSERT row where `post_content` is empty
    $cleanedSql = preg_replace(
      // (5206, 17, '2015-02-06 16:04:40', '2015-02-06 13:04:40', '',
      // (5204, 17, '2015-02-06 15:57:29', '2015-02-06 12:57:29', '',
      "/\([0-9]+,\s[0-9]+(,\s'[0-9\-\s]+:[0-9]+:[0-9]+'){2},\s'',\s.*\),\n/im", // Matches empty `post_content` entries
      '',
      $sql
    );

    // Write the cleaned SQL back to the file
    File::put($path, $cleanedSql);

    $this->info('Empty articles have been removed from the SQL file.');
  }
}
