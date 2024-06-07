<?php

namespace App\Http\Middleware;

use Closure;
use URL;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LanguageMiddleware {

  public function handle(Request $request, Closure $next): Response {
    if (in_array($request->segment(1), config("app.locales"))) {
      URL::defaults(['locale' => $request->segment(1)]);
      app()->setLocale($request->segment(1));
    } else {
      URL::defaults(['locale' => app()->getLocale()]);
      // abort(404);
    }
    $request->route()->forgetParameter('locale');
    return $next($request);
  }
}
