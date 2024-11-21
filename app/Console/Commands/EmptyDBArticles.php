<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\Article;

class EmptyDBArticles extends Command {
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'articles:empty';


  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'get a list of empty articles ids, to remove them later';

  /**
   * Execute the console command.
   */
  public function handle() {

    $articles_ids = Article::select("id")->get()->pluck("id")->toArray();
    $ar_articles_ids = Article::translatedIn("ar")->select("id")->get()->pluck("id")->toArray();

    $arr = array_diff($articles_ids, $ar_articles_ids);
    dd($arr);

    $this->info('All articles imported successfully!');
  }
}
