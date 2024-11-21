<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class PageController extends Controller {

  public function home() {
    $categories = Category::notProduct()->ordered()->translatedIn(app()->getLocale())->limit(8)->get();
    $latestArticles = Article::orderBy("created_at", "DESC")->where("status", 2)->translatedIn(app()->getLocale())->with("user", "category")->limit(3)->get();
    $featuredCourses = Course::orderBy("created_at", "DESC")->limit(3)->get();
    return view("home", compact("latestArticles", "categories", "featuredCourses"));
  }

  public function articles() {
    $popular_categories = Category::notProduct()->mostUsed()->translatedIn(app()->getLocale())->limit(6)->get();
    $popular_tags = Tag::mostUsed()->limit(6)->get();
    $articles = Article::orderBy("created_at", "DESC")->where("status", 2)->translatedIn(app()->getLocale())->with("user", "category")->paginate(config("settings.tables_row_count"));
    return view("articles", compact("articles", "popular_categories", "popular_tags"));
  }

  public function contactus(Request $request) {
    return view("contact-us");
  }
}
