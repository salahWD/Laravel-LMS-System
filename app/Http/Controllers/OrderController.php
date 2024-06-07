<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OrderController extends Controller {

  public function index() {
    //
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

    return view("shop.track", compact("order", "qrCode"));
  }

  public function edit(Order $order) {
    //
  }

  public function update(Request $request, Order $order) {
    //
  }

  public function destroy(Order $order) {
    //
  }
}
