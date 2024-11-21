@extends('layout')

@section('meta')
  <title>{{ __(config('app.name')) . ' | ' . __('checkout') }}</title>
@endsection

@section('styles')
  <style>
    :root {}
  </style>
  @vite(['resources/css/checkout.css'])
  <script src="https://js.stripe.com/v3/"></script>
@endsection

@section('content')
  @if ($errors->any())
    <div class="container">
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  @endif
  <div class="nomargincontainer">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <div class="pb-4 pt-3">
            <div id="error-message"></div>
            <form id="payment-form" style="padding-top: 10px">
              @csrf
              @if (!auth()->check())
                <h3 class="title">{{ __('contact / follow-up') }}</h3>
                <input class="form-control rounded" type="email" name="email" placeholder="{{ __('email') }}">
              @endif
              <h3 class="title mt-4">Shipping</h3>
              <div id="address-element">
                <!--Stripe.js injects the Address Element-->
              </div>
              <hr />
              <h3 class="title mt-3">Payment</h3>
              <div id="payment-element">
                <!--Stripe.js injects the Payment Element-->
              </div>
              <button id="submitBtn">
                <div class="spinner hidden" id="spinner"></div>
                <span id="button-text">Pay now</span>
              </button>
              <div id="payment-message" class="hidden"></div>
            </form>
          </div>
        </div>
        <div class="col-md-6">
          <div class="products">
            <div class="holder">
              <div class="d-flex justify-content-between align-items-baseline">
                <h3 class="title m-0">Products</h3>
                <a href="{{ route('cart_edit') }}" class="fs-6 underline">
                  <u>
                    change order
                    <i class="fa fa-edit"></i>
                  </u>
                </a>
              </div>
              <hr />
              @if ($products != null)
                @foreach ($products as $product)
                  <div class="product">
                    <div class="image">
                      <img src="{{ $product->options->image }}" alt="{{ $product->name }}">
                      <div class="badge bg-dark">{{ $product->qty }}</div>
                    </div>
                    <div class="content">
                      <p class="title">
                        {{ $product->name }}
                      </p>
                    </div>
                    <div class="price">
                      <span>${{ $product->price }}</span>
                    </div>
                  </div>
                @endforeach
              @else
                <div class="alert alert-warning rounded">
                  <p class="lead m-0">No Product To Buy</p>
                </div>
              @endif
              <hr>
              <div class="item">
                <p class="title">Subtotal</p>
                <p class="price">{{ Cart::show_subtotal() }}</p>
              </div>
              {{-- <div class="item">
                  <p class="title">Shipping</p>
                  <p class="price">$20.00</p>
                </div> --}}
              <div class="item">
                <p class="title">shipping</p>
                <p class="price">${{ Cart::tax() }}</p>
              </div>
              <div class="item total">
                <p class="title">Total</p>
                <p class="price">
                  <span class="currency">usd</span>
                  {{ Cart::show_total() }}
                </p>
              </div>
              @error('wrong_total')
                <p class="lead m-0 text-danger">{{ $message }}</p>
              @enderror
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('scripts')
  <script src="{{ url('js/jquery.min.js') }}"></script>
  <script>
    const clientSecretKey = "{{ $intent }}";
    const stripeKey = "{{ config('services.stripe.key') }}";
    const successUrl = "{{ route('checkout_success') }}";
  </script>
  <script src="{{ url('js/checkout.js') }}"></script>
@endsection
