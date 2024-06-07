@extends('layout')

@section('meta')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ __(config("app.name")) . " | " . __("Order Tracking") . ' - ' . $order->token }}</title>
@endsection

@section("styles")
  @vite(['resources/css/track-order.css'])
@endsection

@section("content")
<section class="py-5" style="background: linear-gradient(135deg, #2abde9 0%, #002136 100%);">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-12">
        <h1 class="text-white mb-5 mt-0 text-center text-capitalize">track your order</h1>
        <div class="card card-stepper" style="border-radius: 16px;">
          <div class="card-body p-5">
            <div class="d-flex mb-5 invoice justify-content-between align-items-center">
              <div class="title">
                <h5 class="mb-0">INVOICE <span class="text-primary font-weight-bold">#{{ strtoupper($order->token) }}</span></h5>
              </div>
              <div class="d-flex gap-3 align-items-center flex-wrap justify-content-center">
                <div class="text-end info">
                  <p class="mb-0">Expected Arrival <span>01/12/19</span></p>
                  <p class="mb-0">USPS <span class="font-weight-bold">234094567242423422898</span></p>
                </div>
                <div class="text-center qr">
                  {!! $qrCode !!}
                </div>
              </div>
            </div>

            <div class="max-sm-flex-col">
              <ul class="progressbar d-flex justify-content-between mx-0 mt-0 mb-5 px-0 pt-0 pb-2">
                <li class="step @if($order->stage >= 1) active @endif text-center d-flex align-items-center fas fa-check-circle" id="step1"></li>
                <li class="step @if($order->stage >= 2) active @endif text-center d-flex align-items-center fas fa-check-circle" id="step2"></li>
                <li class="step @if($order->stage >= 3) active @endif text-center d-flex align-items-center fas fa-check-circle" id="step3"></li>
                <li class="step @if($order->stage >= 4) active @endif text-center d-flex align-items-center fa-regular fa-circle" id="step4"></li>
              </ul>

              <div class="progresscards d-flex justify-content-between">
                <div class="d-lg-flex align-items-center @if($order->stage >= 1) text-success @endif">
                  <i class="fas fa-clipboard-list fa-3x me-lg-4 mb-3 mb-lg-0"></i>
                  <div class="title">
                    <p class="fw-bold mb-1">Order</p>
                    <p class="fw-bold mb-0">Processed</p>
                  </div>
                </div>
                <div class="d-lg-flex align-items-center @if($order->stage >= 2) text-success @endif">
                  <i class="fas fa-box-open fa-3x me-lg-4 mb-3 mb-lg-0"></i>
                  <div class="title">
                    <p class="fw-bold mb-1">Order</p>
                    <p class="fw-bold mb-0">Shipped</p>
                  </div>
                </div>
                <div class="d-lg-flex align-items-center @if($order->stage >= 3) text-success @endif">
                  <i class="fas fa-shipping-fast fa-3x me-lg-4 mb-3 mb-lg-0"></i>
                  <div class="title">
                    <p class="fw-bold mb-1">Order</p>
                    <p class="fw-bold mb-0">En Route</p>
                  </div>
                </div>
                <div class="d-lg-flex align-items-center @if($order->stage >= 4) text-success @endif">
                  <i class="fas fa-home fa-3x me-lg-4 mb-3 mb-lg-0"></i>
                  <div class="title">
                    <p class="fw-bold mb-1">Order</p>
                    <p class="fw-bold mb-0">Arrived</p>
                  </div>
                </div>
              </div>
            </div>

          </div>

        </div>
      </div>
    </div>
  </div>
</section>
@endsection
