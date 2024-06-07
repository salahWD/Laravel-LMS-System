<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Cart;
use App\Models\Order;

class CartController extends Controller {

  public function show() {
    if (!auth()->check()) {
      $u = new User();
    } else {
      Cart::restore(auth()->user()->id);
    }

    $intent = "";

    try {
      $intent = auth()->check() ? auth()->user()->createSetupIntent() : $u->createSetupIntent();
      $intent = $intent->client_secret;
    } catch (\Exception $e) {
      if (auth()->check()) {
        auth()->user()->stripe_id = NULL;
        auth()->user()->save();
        // try {
        //   $intent = auth()->check() ? auth()->user()->createSetupIntent() : $u->createSetupIntent();
        //   $intent = $intent->client_secret;
        // } catch (\Exception $e) {
        // }
      }
    }
    return view("shop.checkout")->with([
      "intent" => $intent,
      "products" => Cart::content(),
    ]);
  }

  public function success() {
    dd("success page");
    return view("shop.thanks");
  }

  public function checkout(Request $request) {
    $request->validate([
      "paymentMethod" => "required|string",
      "address" => "required|json",
      "email" => "nullable|email",
    ]);

    if (!auth()->check()) {
      $request->validate([
        "email" => "required|email",
      ]);
    }

    $address = json_decode(request("address"));

    if (auth()->check()) {
      $user = auth()->user();
    } else {
      $user = new User();
    }
    if (Cart::total() <= 0) {
      return redirect()->route("cart_show")->withErrors(['wrong_total' => 'Total Amount is wrong']);
    }
    $products = [];
    $products_ids = [];
    if (Cart::content()) {
      $description = Cart::content()->first()->name .
        " and other (" . Cart::content()->count() . ") products ordered by (" .
        $address->name . "), address: " .
        format_address($address->address);
      foreach (Cart::content() as $item) {
        $products[$item->id . "_" . substr($item->name, 0, 32)] = $item->qty;
        $products_ids[$item->id] = ["quantity" => $item->qty];
      }
    }

    try {
      // $user->createOrGetStripeCustomer();
      // $customer = (new User)->createOrGetStripeCustomer($email, $paymentMethod);

      $stripeCharge = $user->charge(Cart::total() * 100, request("paymentMethod"), [
        'receipt_email' => $user->email ?? "salahb170@gmail.com",
        'description' => $description ?? "new product perchuse",
        'metadata' => [
          "email" => $user->email ?? "no email",
          "name" => $address->name,
          ...$products
        ],
        'return_url' => route('checkout_success'),
      ]);

      $order = Order::create([
        "user_id" => auth()->user()->id ?? null,
        "total" => Cart::total() * 100,
        "client_name" => $address->name,
        "address" => format_address($address->address),
      ]);

      $order->products()->attach($products_ids);

      Cart::deleteStoredCart(auth()->user()->id);
      Cart::destroy();

      return redirect()->route("order_tracking", $order->id);
    } catch (Exception $e) {
      dd($e);
      return redirect()->route("cart_show")->withErrors(['something' => 'something went wrong']);
    }
  }
}

function format_address($address_obj) {
  return $address_obj->country . ", " .
    $address_obj->state . ", " .
    $address_obj->city . " | " .
    $address_obj->line2 . ", " .
    $address_obj->line1 . ", postal: " .
    $address_obj->postal_code;
}
