<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cart;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class CartController extends Controller {

  public function __construct() {
    $this->middleware('auth')->except("webhook");// only loged in users can use this controller
  }

  public function show() {

    // if (auth()->check()) {
      Cart::restore(auth()->user()->id);
    // }

    if (Cart::total() > 0) {

      $stripe = new \Stripe\StripeClient(config("services.stripe.secret"));

      $intent = $stripe->paymentIntents->create([
        'currency' => config("cart.currency_name") ?? "usd",
        'amount' => Cart::total() * 100,
        'automatic_payment_methods' => ['enabled' => true],
        /* =============== if unsigned people can buy stuff =============== */
        // 'metadata' => [
        //   'email' => auth()->check() ? auth()->user()->email : "no email",
        //   'user_id' => auth()->check() ? auth()->user()->id : "not signed in",
        // ],
        'description' => auth()->user()->fullname() . " Ordered " . Cart::content()->count() .  " Product",
        'metadata' => [
          'email' => auth()->user()->email,
          'user_id' => auth()->user()->id,
        ],
      ]);

      /* ========= Note ============
        if not signed in this will make sure to save the orders in a new cart and
        the id of the cart will be known, so we can retrieve it later when needed
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

      return view("shop.checkout")->with([
        "intent" => $intent->client_secret,
        "products" => Cart::content(),
      ]);
    }
    return view("shop.error");
  }

  public function webhook() {

    $input = file_get_contents('php://input');
    $body = json_decode($input);
    $event = null;

    try {
      $event = \Stripe\Webhook::constructEvent(
        $input,
        $_SERVER['HTTP_STRIPE_SIGNATURE'],
        config("services.stripe.webhook_secret")
      );
    } catch (Exception $e) {
      http_response_code(403);
      echo json_encode(['error' => $e->getMessage()]);
      exit;
    }

    if ($event->type == 'payment_intent.succeeded') {

      $products_ids = [];

      $address = $event->data->object->shipping->address;

      Cart::restore($event->data->object?->metadata?->user_id);

      if (Cart::content()) {
        foreach (Cart::content() as $item) {
          $products_ids[$item->id] = ["quantity" => $item->qty];
        }
      }

      $order = Order::updateOrCreate(
        [
          "user_id" => $event->data->object?->metadata?->user_id,
          "intent_id" => $event->data->object->id,
        ],
        [
          "total" => Cart::total() * 100,
          "client_name" => $event->data->object->shipping?->name,
          "stage" => "1",
          "address" => format_address($address),
        ]
      );
      // $order = Order::create([
      //   "user_id" => $event->data->object?->metadata?->user_id,
      //   "total" => Cart::total() * 100,
      //   "intent_id" => $event->data->object->id,
      //   "client_name" => $event->data->object->shipping?->name,
      //   "stage" => "1",
      //   "address" => format_address($address),
      // ]);

      $order->products()->attach($products_ids);

      if ($order->user_id != null) {
        Cart::deleteStoredCart($order?->user_id);
      } else {
        Cart::deleteStoredCart($event->data->object->id);
      }

      Cart::destroy();

      return ['status' => 'success'];

    } else if ($event->type == 'payment_intent.payment_failed') {

      if ($event->data->object?->metadata?->user_id != null) {
        Cart::deleteStoredCart($event->data->object?->metadata?->user_id);
      } else {
        Cart::deleteStoredCart($event->data->object->id);
      }

      abort(404);

    }
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

  // public function checkout(Request $request) {
  //   $request->validate([
  //     "paymentMethod" => "required|string",
  //     "address" => "required|json",
  //     "email" => "nullable|email",
  //   ]);

  //   if (!auth()->check()) {
  //     $request->validate([
  //       "email" => "required|email",
  //     ]);
  //   }

  //   $address = json_decode(request("address"));

  //   if (auth()->check()) {
  //     $user = auth()->user();
  //   } else {
  //     // $user = User::find(4);
  //   }
  //   if (Cart::total() <= 0) {
  //     return redirect()->route("cart_show")->withErrors(['wrong_total' => 'Total Amount is wrong']);
  //   }
  //   $products = [];
  //   $products_ids = [];
  //   if (Cart::content()) {
  //     $description = Cart::content()->first()->name .
  //       " and other (" . Cart::content()->count() . ") products ordered by (" .
  //       $address->name . "), address: " .
  //       format_address($address->address);
  //     foreach (Cart::content() as $item) {
  //       $products[$item->id . "_" . substr($item->name, 0, 32)] = $item->qty;
  //       $products_ids[$item->id] = ["quantity" => $item->qty];
  //     }
  //   }

  //   try {
  //     // $user->createOrGetStripeCustomer();
  //     // dd(request()->all());
  //     // $customer = $user->createOrGetStripeCustomer(/* request("email") ?? "test@test.test", request("paymentMethod") */);
  //     // dd($customer);

  //     $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

  //     $charge = $stripe->charges->create([
  //       'currency' => config("cart.currency_name") ?? "usd",
  //       'amount' => Cart::total() * 100,
  //       'source'  => request("paymentMethod"), // your stripe token goes here
  //       'statement_descriptor' => 'Short description',
  //       'description' => 'Long description',
  //     ]);
  //     dd($charge, $stripe);

  //     // $stripeCharge = $user->charge(Cart::total() * 100, request("paymentMethod"), [
  //     //   'receipt_email' => $user->email ?? "salahb170@gmail.com",
  //     //   'description' => $description ?? "new product perchuse",
  //     //   'metadata' => [
  //     //     "email" => $user->email ?? "no email",
  //     //     "name" => $address->name,
  //     //     ...$products
  //     //   ],
  //     //   'return_url' => route('checkout_success'),
  //     // ]);

  //     $order = Order::create([
  //       "user_id" => auth()->user()->id ?? null,
  //       "total" => Cart::total() * 100,
  //       "client_name" => $address->name,
  //       "address" => format_address($address->address),
  //     ]);

  //     $order->products()->attach($products_ids);

  //     Cart::deleteStoredCart(auth()->user()->id);
  //     Cart::destroy();

  //     return redirect()->route("order_tracking", $order->id);
  //   } catch (Exception $e) {
  //     dd($e);
  //     return redirect()->route("cart_show")->withErrors(['something' => 'something went wrong']);
  //   }
  // }
}

function format_address($address_obj) {
  return $address_obj->country . ", " .
    $address_obj->state . ", " .
    $address_obj->city . " | " .
    $address_obj->line2 . ", " .
    $address_obj->line1 . ", postal: " .
    $address_obj->postal_code;
}
