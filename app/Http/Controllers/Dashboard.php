<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Certificate;
use App\Models\Lecture;
use App\Models\Product;
use App\Models\Message;
use App\Models\Tag;
use App\Models\Order;
use App\Models\Appointment;
use App\Helpers\ConfigHelper;

class Dashboard extends Controller {

  public function users(Request $request) {
    $users = User::paginate(15);

    return view('dashboard.users', compact("users"));
  }

  public function articles(Request $request) {
    $articles = Article::orderBy("created_at", "DESC")->paginate(15);

    return view('dashboard.articles', compact("articles"));
  }

  public function categories(Request $request) {
    $categories = Category::notProduct()->orderBy("created_at", "DESC")->paginate(15);

    return view('dashboard.categories', compact("categories"));
  }

  public function courses(Request $request) {
    $courses = Course::withCount("items")->orderBy("created_at", "DESC")->paginate(15);

    return view('dashboard.courses', compact("courses"));
  }

  public function lectures(Request $request) {
    $lectures = Lecture::with("courseItem")->with("courseItem.course")->orderBy("created_at", "DESC")->paginate(15);

    return view('dashboard.lectures', compact("lectures"));
  }

  public function comments(Request $request) {
    $comments = Comment::orderBy("created_at", "DESC")->where("approved", 0)->paginate(15); // comment pagination

    return view('dashboard.comments', compact("comments"));
  }

  public function certificates(Request $request) {
    $certificates = Certificate::orderBy("created_at", "DESC")->paginate(15);

    return view('dashboard.certificates', compact("certificates"));
  }

  public function tags(Request $request) {
    $tags = Tag::withCount("articles")->paginate(15);

    return view('dashboard.tags', compact("tags"));
  }

  public function tag(Request $request, Tag $tag) {
    $articles = $tag->articles()->orderBy("created_at", "DESC")->limit(5)->get();
    return view('dashboard.tag', compact("tag", "articles"));
  }

  public function products(Request $request) {
    $products = Product::with("category")->orderBy("created_at", "DESC")->paginate(15);
    return view('dashboard.products.index', compact("products"));
  }

  public function orders(Request $request) {
    $orders = Order::with("user")->real()->orderBy("created_at", "DESC")->paginate(15);
    return view('dashboard.orders.index', compact("orders"));
  }

  public function messages(Request $request) {
    $messages = Message::orderBy("created_at", "DESC")->where("is_website", 0)->with("user")->paginate(15);
    return view('dashboard.messages', compact("messages"));
  }

  public function settings(Request $request) {
    return view('dashboard.settings');
  }

  public function set_settings(Request $request) {
    $request->validate([
      "certification_section" => "sometimes|in:on,off",
    ]);

    if (request("certification_section") != null && !empty(request("certification_section")) && request("certification_section")) {

      ConfigHelper::updateDashboardConfig([
        "certificates_status" => true,
      ]);
    } else {
      ConfigHelper::updateDashboardConfig([
        "certificates_status" => false,
      ]);
    }

    return redirect()->route('dashboard_settings');
  }
}
