<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cart;
use App\Models\Order;
use App\Models\Product;

class CartController extends Controller {

  public function __construct() {
    $this->middleware('auth'); // only loged in users can use this controller
  }

  public function edit() {
    return view("shop.edit-cart")->with([
      "products" => Cart::content(),
      "cart" => Cart::instance(),
    ]);
  }

  public function update(Request $request) {

    $validated = $request->validate([
      "action" => "required|in:shopping,checkout",
      "products" => "nullable|array",
      "products.*.row_id" => "required|string|min:30|max:34",
      "products.*.qty" => "required|numeric|min:1",
    ]);

    if (!empty($validated["products"])) {
      $ids = collect($validated["products"])->pluck("row_id")->toArray();

      foreach (Cart::content() as $key => $product) {
        if (!in_array($key, $ids)) {
          Cart::remove($key);
        }
      }

      foreach ($validated["products"] as $prod) {
        Cart::update($prod["row_id"], $prod["qty"]);
      }

      Cart::store(auth()->user()->id);
    } else {
      Cart::destroy();
      Cart::deleteStoredCart(auth()->user()->id);
    }

    if ($validated["action"] == "checkout") {
      return redirect()->route("cart_show");
    } else {
      return redirect()->route("shop");
    }
  }

  public function show() {
    /* ====== time testing ====== */
    // $start = microtime(true);

    Cart::restore(auth()->user()->id);

    $ids = collect(Cart::content())->pluck("id");

    $products = Product::whereIn("id", $ids)->get();

    foreach (Cart::content() as $pro) {
      $pro->price = $products->where("id", $pro->id)->first()->price;
    }

    Cart::store(auth()->user()->id);

    /* ====== time testing ====== */
    // dump("time to finish updating the cart content prices is: " . microtime(true) - $start);

    if (Cart::total() > 0) {

      $stripe = new \Stripe\StripeClient(config("services.stripe.secret"));

      $intent = $stripe->paymentIntents->create([
        'currency' => config("cart.currency_name") ?? "usd",
        'amount' => Cart::total() * 100,
        'automatic_payment_methods' => ['enabled' => true],
        /* =============== if unsigned people can buy stuff =============== */
        // 'metadata' => [
        //   'product_type' => 'products',
        //   'email' => auth()->check() ? auth()->user()->email : "no email",
        //   'user_id' => auth()->check() ? auth()->user()->id : "not signed in",
        // ],
        'description' => auth()->user()->fullname() . " Ordered " . Cart::content()->count() .  " Product",
        'metadata' => [
          'product_type' => 'products',
          'fullname' => auth()->user()->fullname(),
          'username' => auth()->user()->username,
          'email' => auth()->user()->email,
          'user_id' => auth()->user()->id,
        ],
      ]);

      /* ========= Note ============
        if user is not signed in this will make sure to save the orders in a new cart and
        the id of the cart will be known as the intent_id, so we can retrieve it later when needed
        ============================ */

      // if (!auth()->check()) {
      //   $content = Cart::content();
      //   Cart::destroy();
      //   Cart::restore($intent->id);
      //   foreach ($content as $cartItem) {
      //     Cart::add($cartItem->id, $cartItem->name, $cartItem->qty, $cartItem->price, $cartItem->options->toArray());
      //   }
      //   Cart::store($intent->id);
      // }

      /* ====== time testing ====== */
      // dd("time to return the view after getting the stripe intention id: " . microtime(true) - $start);

      return view("shop.checkout")->with([
        "intent" => $intent->client_secret,
        "products" => Cart::content(),
      ]);
    }
    return view("shop.error");
  }

  public function success(Request $request) {

    Cart::destroy();

    $intent = request("payment_intent");

    $order = Order::firstOrCreate(
      [
        "user_id" => auth()->user()->id,
        "intent_id" => $intent,
      ],
      [
        "total" => Cart::total() * 100,
        "stage" => "0",
        "address" => "not signed",
      ]
    );

    if ($intent != null) {

      return view("shop.thanks", ["order" => $order]);
    } else {

      return view("shop.error");
    }
  }
}
