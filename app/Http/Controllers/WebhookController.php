<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cart;
use App\Models\Order;
use App\Models\BookedAppointment;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;

class WebhookController extends Controller {
  public function webhook(Request $request) {

    Stripe::setApiKey(config("services.stripe.secret"));

    $event = null;

    try {
      $event = Webhook::constructEvent(
        $request->getContent(),
        $_SERVER['HTTP_STRIPE_SIGNATURE'],
        config("services.stripe.webhook_secret")
      );
    } catch (Exception $e) {
      http_response_code(403);
      echo json_encode(['error' => $e->getMessage()]);
      exit;
    }

    if ($event->type == 'payment_intent.succeeded') {

      $metadata = $event->data->object?->metadata;

      // Perform different actions based on the product or service
      if ($metadata->product_type == 'course') {
        // $this->enrollUserInCourse($event);
      } else if ($metadata->product_type == 'appointment') {
        $this->appointmentCheckout($event);
      } else if ($metadata->product_type == 'products') {
        $this->cartCheckout($event);
      }

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

  public function appointmentCheckout($event) {

    // $bookedAppointment = BookedAppointment::where("status", 2)->where("secret_key", $event->data->object->client_secret)->first();
    $bookedAppointment = BookedAppointment::where("status", 2)->where("secret_key", $event->data->object->id)->first();
    if ($bookedAppointment) {
      $bookedAppointment->status = 1;
      $bookedAppointment->save();
    }
  }

  public function cartCheckout($event) {
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
  }
}
