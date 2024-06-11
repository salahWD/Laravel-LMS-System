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
                  <p class="mb-0">Expected Arrival: <span>01/12/19</span></p>
                  <p class="mb-0">USPS: <span class="font-weight-bold">234094567242423422898</span></p>
                </div>
                <div class="text-center qr">
                  {!! $qrCode !!}
                </div>
              </div>
            </div>

            <div class="max-sm-flex-col">
              <ul class="progressbar d-flex justify-content-between mx-0 mt-0 mb-5 px-0 pt-0 pb-2">
                <li class="step @if($order->stage >= 1) active @endif text-center d-flex align-items-center @if($order->stage >= 1) fas fa-check-circle @else fa-regular fa-circle @endif" id="step1"></li>
                <li class="step @if($order->stage >= 2) active @endif text-center d-flex align-items-center @if($order->stage >= 2) fas fa-check-circle @else fa-regular fa-circle @endif" id="step2"></li>
                <li class="step @if($order->stage >= 3) active @endif text-center d-flex align-items-center @if($order->stage >= 3) fas fa-check-circle @else fa-regular fa-circle @endif" id="step3"></li>
                <li class="step @if($order->stage >= 4) active @endif text-center d-flex align-items-center @if($order->stage >= 4) fas fa-check-circle @else fa-regular fa-circle @endif" id="step4"></li>
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

          @if($products->count() > 0)
            <div style="margin: 4rem auto; width: 100%; overflow: hidden; border-radius: 0.5rem; --tw-bg-opacity: 1; padding-left: 1rem; padding-right: 1rem;">
              <div style="display: block; border-radius: 0.5rem; border-width: 1px; --tw-border-opacity: 1; border-color: rgb(156 163 175 / var(--tw-border-opacity)); --tw-bg-opacity: 1;">
                <div style="border-bottom-width: 2px; --tw-border-opacity: 1; border-color: rgb(245 245 245 / var(--tw-border-opacity)); padding: 0.75rem 1.5rem;">
                  <div style="display: flex; align-items: center; justify-content: space-between; --tw-bg-opacity: 1; background-color: rgb(243 244 246 / var(--tw-bg-opacity)); padding: 0.75rem 2.5rem;">
                    <h3 style="font-size: inherit; font-weight: 600; margin: 0px; --tw-text-opacity: 1; color: rgb(17 24 39 / var(--tw-text-opacity));">
                      ({{ $products->reduce(fn($carry, $product) => $carry + $product->pivot->quantity, 0) }}) Items
                    </h3>
                    <h3 style="font-size: inherit; font-weight: 600; margin: 0px; --tw-text-opacity: 1; color: rgb(17 24 39 / var(--tw-text-opacity));">
                      Total: {{ config("cart.currency") . "" . $order->calc_price() }}
                    </h3>
                  </div>
                </div>
                <div style="padding: 1.5rem;">
                  <div style="display: flex; flex-direction: column;">
                    <div style="overflow-x: auto;">
                      <div style="display: inline-block; min-width: 100%; padding-top: 0.5rem; padding-bottom: 0.5rem;">
                        <div style="overflow: hidden;">
                          <table style="text-indent: 0px; border-color: inherit; border-collapse: collapse; min-width: 100%; text-align: left; font-size: 0.875rem; line-height: 1.25rem; font-weight: 300;">
                            <thead style="border-bottom-width: 1px; font-size: 1rem; line-height: 1.5rem; font-weight: 500; --tw-text-opacity: 1; color: rgb(17 24 39 / var(--tw-text-opacity));">
                              <tr>
                                <th scope="col" style="padding: 1rem 1.5rem;">
                                  Image
                                </th>
                                <th scope="col" style="padding: 1rem 1.5rem;">
                                  Product Name
                                </th>
                                <th scope="col" style="padding: 1rem 1.5rem;">
                                  Quantity
                                </th>
                                <th scope="col" style="padding: 1rem 1.5rem;">
                                  Price
                                </th>
                              </tr>
                            </thead>
                            <tbody style="font-weight: 500; --tw-text-opacity: 1; color: rgb(31 41 55 / var(--tw-text-opacity));">
                              @foreach($products as $product)
                                <tr style="border-bottom-width: 1px;">
                                  <td style="white-space: nowrap; padding: 1rem 1.5rem;">
                                    <img src="{{ $product->main_image_url() }}"
                                      alt="Product Image"
                                      style="display: block; vertical-align: middle; max-width: 100%; height: 8rem; width: 8rem; border-radius: 0.25rem; object-fit: cover;" />
                                  </td>
                                  <td style="white-space: nowrap; padding: 1rem 1.5rem;">
                                    {{ $product->title }}
                                  </td>
                                  <td style="white-space: nowrap; padding: 1rem 1.5rem;">
                                    {{ $product->pivot->quantity }}
                                  </td>
                                  <td style="white-space: nowrap; padding: 1rem 1.5rem;">
                                    {{ config('cart.currency') . $product->price }}
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @else
            <div class="card-body">
              <div class="my-4">
                <div role="alert"
                  style="border-style: solid; border-width: 0px;border-left-width: 4px; border-color: rgb(249 115 22); background-color: rgb(255 237 213); padding: 1rem; color: rgb(194 65 12);">
                  <p class="font-bold" style="margin: 0px; font-weight: 700;">Be Warned</p>
                  <p style="margin: 0px;">There is no products in this order.</p>
                </div>
              </div>
            </div>
          @endif

        </div>
      </div>
    </div>
  </div>
</section>
@endsection
