@extends('layout')

@section('meta')
  <title>{{ __(config("app.name")) . " | " . __("checkout") }}</title>
@endsection

@section("styles")
  <style>
    :root {
    }
  </style>
  @vite([
    'resources/css/checkout.css',
  ])
  <script src="https://js.stripe.com/v3/"></script>
@endsection

@section("content")
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
          {{-- <hr> --}}
          {{-- <div class="info">
            <h3 class="title">{{ __("delivery") }}</h3>
            <div class="form-floating">
              <select class="form-select" id="country" aria-label="Floating label select example">
                <option selected>turkey</option>
                <option value="1">united kingdom</option>
                <option value="2">united stats</option>
              </select>
              <label for="country">country/region</label>
            </div>
            <div class="row mt-md-3">
              <div class="col-md-6">
                <div class="form-floating mb-3">
                  <input type="text" class="form-control" id="first-name" placeholder="">
                  <label for="first-name">First name (optional)</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating mb-3">
                  <input type="text" class="form-control" id="last-name" placeholder="">
                  <label for="last-name">Last name</label>
                </div>
              </div>
            </div>
            <div class="form-floating mb-3">
              <input type="text" class="form-control" id="address" placeholder="">
              <label for="address">address</label>
            </div>
            <div class="form-floating mb-3">
              <input type="text" class="form-control" id="addres2" placeholder="">
              <label for="addres2">apartment, suite, etc. (optional)</label>
            </div>
            <div class="row mt-md-3">
              <div class="col-md-6">
                <div class="form-floating mb-3">
                  <input type="text" class="form-control" id="postal" placeholder="">
                  <label for="postal">postal code</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating mb-3">
                  <input type="text" class="form-control" id="city" placeholder="">
                  <label for="city">City</label>
                </div>
              </div>
            </div>
            <h4 class="title">{{ __("shipping method") }}</h4>
            <div class="form-floating">
              <select class="form-select" id="shipping" aria-label="Floating label select example">
                <option selected>International Shipping</option>
                <option value="1">usd Shipping</option>
                <option value="2">custom Shipping</option>
              </select>
              <label for="shipping">available methods</label>
            </div>
            <h3 class="title">{{ __("payment") }}</h3>
            <p class="lead">All transactions are secure and encrypted.</p>
          </div> --}}
          <form id="payment-form" style="padding-top: 10px" method="POST" action="{{ route("checkout") }}">
            @csrf
            @if (!auth()->check())
              <h3 class="title">{{ __("contact / follow-up") }}</h3>
              <input class="form-control" type="email" name="email" placeholder="{{ __("email") }}">
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
        <div class="col-md-6">
          <div class="products">
            <div class="holder">
              <h3 class="title m-0">Products</h3>
              <hr />
              @if ($products != null)
                @foreach($products as $product)
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
                <div class="alert alert-warning rounded"><p class="lead m-0">No Product To Buy</p></div>
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
              @error("wrong_total") <p class="lead m-0 text-danger">{{ $message }}</p> @enderror
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section("scripts")
<script src="{{ url("js/jquery.min.js") }}"></script>
<script>
    const clientSecretKey = "{{ $intent }}";
    const stripeKey = "{{ env('STRIPE_KEY') }}";
    const successUrl = "{{ route('checkout_success') }}";
  </script>
  <script src="{{ url("js/checkout.js") }}"></script>
@endsection
