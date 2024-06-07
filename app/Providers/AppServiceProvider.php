<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Cart;
// use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider {


  public function register(): void {
    //
  }

  public function boot(): void {

    Paginator::defaultView('vendor.pagination.website');

    view()->composer("layout.header", function ($view) {
      $view->with("all_courses", \App\Models\Course::orderBy("created_at", "DESC")->select(["title", "id"])->limit(5)->get());
    });
    view()->composer("shop.cart", function ($view) {
      if (auth()->check()) {
        Cart::restore(auth()->user()->id);
      }
      // Cart::destroy();
      $view->with("cart_items_count", Cart::count() ?? 0);
    });

    // DB::listen(function ($query) {
    //   echo '<pre>';
    //   var_dump([
    //     $query->sql,
    //     $query->bindings,
    //     $query->time
    //   ]);
    //   echo '</pre>';
    // });
  }
}
