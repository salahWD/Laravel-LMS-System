<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OrderController extends Controller {

  public function __construct() {
    $this->middleware('auth'); // only loged in users can use this controller
  }

  public function index() {
    $user = auth()->user();
    $orders = Order::where("user_id", $user->id)->where("stage", ">", 0)->with("products")->paginate(config("settings.tables_row_count"));
    // foreach($orders as $o) {
    //   dump($o->products);
    //   dump($o->calc_price());
    //   dump($o->calc_quantity());
    // }
    // dd("end");
    return view('profile.orders', compact('orders', 'user'));
  }

  public function create() {
    //
  }

  public function store(Request $request) {
    //
  }

  public function show(Order $order) {
    $trackingUrl = route("order_tracking", ["order" => $order->token]);
    $qrCode = QrCode::size(160)->generate($trackingUrl);
    $products = $order->products()->withPivot("quantity")->get();

    return view("shop.track", compact("order", "qrCode", "products"));
  }

  public function edit(Order $order) {
    $stripe = new \Stripe\StripeClient(config("services.stripe.secret"));

    $payment = $stripe->paymentIntents->retrieve($order->intent_id, []);

    return view("orders.edit", compact('order', 'payment'));
  }

  public function update(Request $request, Order $order) {
    $request->validate([
      "stage" => "required|integer",
    ]);
    $order->stage = intval($request->input("stage", "2") + 1);
    $order->save();
    return redirect()->route("order_edit", $order->id);
  }

  /* refund the payment and remove the order */
  public function destroy(Order $order) {
    dd("why tho ?");

    $stripe = new \Stripe\StripeClient(config("services.stripe.secret"));

    $charges = $stripe->charges->all(['payment_intent' => $order->intent_id]);
    $item = $charges?->data;
    $last_item = end($item);

    if (!$last_item->refunded) {
      $stripe->refunds->create(['payment_intent' => $order->intent_id]);
    }
    $order->delete();

    return redirect()->route("orders_manage");
  }
}
