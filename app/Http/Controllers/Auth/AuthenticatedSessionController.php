<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Cart;

class AuthenticatedSessionController extends Controller {
  /**
   * Display the login view.
   */
  public function create(): View {
    return view('auth.login');
  }

  /**
   * Handle an incoming authentication request.
   */
  public function store(LoginRequest $request): RedirectResponse {
    $request->authenticate();

    $request->session()->regenerate();

    if (Cart::count() > 0 && auth()->check()) {
      $content = Cart::content();
      Cart::destroy();
      Cart::restore(auth()->user()->id);
      foreach ($content as $cartItem) {
        Cart::add($cartItem->id, $cartItem->name, $cartItem->qty, $cartItem->price, $cartItem->options->toArray());
      }
      Cart::store(auth()->user()->id);
    }

    if (auth()->check() && auth()->user()->is_admin()) {
      return redirect()->intended(RouteServiceProvider::ADMIN_HOME);
    }
    return redirect()->intended(RouteServiceProvider::HOME);
  }

  /**
   * Destroy an authenticated session.
   */
  public function destroy(Request $request): RedirectResponse {
    Auth::guard('web')->logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect('/');
  }
}
