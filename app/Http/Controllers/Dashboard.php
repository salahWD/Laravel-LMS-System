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
use App\Helpers\ConfigHelper;
use Illuminate\Support\Facades\Artisan;
use App\Services\GoogleAnalyticsService;


class Dashboard extends Controller {

  public function index(Request $request) {
    $students_count = 10;

    $totalUsers = GoogleAnalyticsService::getTotalUsers();
    $topCountries = GoogleAnalyticsService::getTopCountries();
    $topPages = GoogleAnalyticsService::getTopPages();
    $totalBounceRate = GoogleAnalyticsService::getTotalBounceRate() ?? 0.22;
    $dailyTraffic = GoogleAnalyticsService::getDailyTrafficLastMonth();

    // dd($totalUsers, $topCountries, $topPages, $totalBounceRate, $dailyTraffic);
    return view('dashboard', compact("students_count", "totalUsers", "topCountries", "topPages", "totalBounceRate", "dailyTraffic"));
  }

  public function users(Request $request) {
    $users = User::paginate(config('settings.tables_row_count'));

    return view('dashboard.users', compact("users"));
  }

  public function articles(Request $request) {
    $articles = Article::orderBy("created_at", "DESC")->paginate(config('settings.tables_row_count'));

    return view('dashboard.articles', compact("articles"));
  }

  public function categories(Request $request) {
    $categories = Category::notProduct()->orderBy("created_at", "DESC")->paginate(config('settings.tables_row_count'));

    return view('dashboard.categories', compact("categories"));
  }

  public function courses(Request $request) {
    $courses = Course::withCount("items")->orderBy("created_at", "DESC")->paginate(config('settings.tables_row_count'));

    return view('dashboard.courses', compact("courses"));
  }

  public function lectures(Request $request) {
    $lectures = Lecture::with("courseItem")->with("courseItem.course")->orderBy("created_at", "DESC")->paginate(config('settings.tables_row_count'));

    return view('dashboard.lectures', compact("lectures"));
  }

  public function comments(Request $request) {
    $comments = Comment::orderBy("created_at", "DESC")->where("approved", 0)->paginate(config('settings.tables_row_count')); // comment pagination

    return view('dashboard.comments', compact("comments"));
  }

  public function certificates(Request $request) {
    $certificates = Certificate::orderBy("created_at", "DESC")->paginate(config('settings.tables_row_count'));

    return view('dashboard.certificates', compact("certificates"));
  }

  public function tags(Request $request) {
    $tags = Tag::withCount("articles")->paginate(config('settings.tables_row_count'));

    return view('dashboard.tags', compact("tags"));
  }

  public function products(Request $request) {
    $products = Product::with("category")->orderBy("created_at", "DESC")->paginate(config('settings.tables_row_count'));
    return view('dashboard.products.index', compact("products"));
  }

  public function orders(Request $request) {
    $orders = Order::with("user")->real()->orderBy("created_at", "DESC")->paginate(config('settings.tables_row_count'));
    return view('dashboard.orders.index', compact("orders"));
  }

  public function messages(Request $request) {
    $messages = Message::orderBy("created_at", "DESC")->where("is_website", 0)->with("user")->paginate(config('settings.tables_row_count'));
    return view('dashboard.messages', compact("messages"));
  }

  public function settings(Request $request) {
    return view('dashboard.settings');
  }

  public function set_settings(Request $request) {
    $request->validate([
      "certification_section" => "sometimes|in:on,off",
      "shop_section" => "sometimes|in:on,off",
      "comments_approval" => "sometimes|in:on,off",
      "tables_row_count" => "required|numeric|min:5|max:100",
    ]);

    ConfigHelper::updateDashboardConfig([
      // "certificates_status" => request("certification_section") == true ?? false,
      "shop_status" => request("shop_section") == true ?? false,
      "comments_approval" => request("comments_approval") == true ?? false,
      "tables_row_count" => request("tables_row_count") != null ? intval(request("tables_row_count")) : 0,
    ]);

    Artisan::call('config:clear');

    return redirect()->route('dashboard_settings');
  }
}
