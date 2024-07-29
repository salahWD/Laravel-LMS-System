@extends('layout')

@section('meta')
  <title>{{ __(config('app.name')) . ' | ' . __('edit cart') }}</title>
@endsection

@section('styles')
  <style>
    :root {}
  </style>
  @vite(['resources/css/edit-cart.css'])
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

  <form action="{{ route('cart_update') }}" method="post" id="cart-form">
    @csrf

    <div class="container mt-5">
      <div style="max-width: 48rem; margin: auto;">
        <h2 style="font-size: 1.5rem;line-height: 2rem;font-weight: 600;margin: 0">
          Shopping Cart</h2>
        <div style="margin-top: 2rem;">
          <table style="font-size: 1rem;line-height: 1.5rem;text-align: left;width: 100%;table-layout: fixed;">
            <tbody style="min-width: 14rem;padding-bottom: 1rem;padding-top: 1rem;white-space: nowrap;width: 24rem;">
              @foreach ($products as $i => $product)
                <tr class="product" data-price="{{ $product->price() }}" id="product-{{ $product->id }}">
                  <input type="hidden" class="d-none" type="text" name="products[{{ $i }}][row_id]"
                    value="{{ $product->rowId }}">
                  <td class="title">
                    <div class="image">
                      <img src="{{ $product->options->image }}" alt="imac image">
                    </div>
                    <span>
                      {{ $product->name }}
                    </span>
                  </td>

                  <td class="qty">
                    <label for="product-qty-{{ $product->id }}"
                      style="clip: rect(0, 0, 0, 0);border-width: 0;height: 1px;margin: -1px;overflow: hidden;padding: 0;position: absolute;white-space: nowrap;width: 1px;">Choose
                      quantity:</label>
                    <div style="width: fit-content">
                      <button type="button" class="qty-btn sub m-auto" data-target="product-qty-{{ $product->id }}">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 1h16"></path>
                        </svg>
                      </button>
                      <input type="number" min="1" id="product-qty-{{ $product->id }}" class="qty-input"
                        data-id="{{ $product->id }}" name="products[{{ $i }}][qty]" placeholder="quantity"
                        value="{{ $product->qty }}" required="">
                      <button type="button" class="qty-btn add m-auto" data-target="product-qty-{{ $product->id }}">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 1v16M1 9h16"></path>
                        </svg>
                      </button>
                    </div>
                  </td>

                  <td class="price" id="product-price-{{ $product->id }}"
                    data-currecny="{{ config('cart.currency') }}" data-price="{{ $product->price }}">
                    {{ config('cart.currency') . $product->subtotal() }}</td>

                  <td class="actions">
                    <button type="button" class="delete-product" data-id="{{ $product->id }}">
                      <svg style="width: 1.25rem;height: 1.25rem;display: block;vertical-align: middle;"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z">
                        </path>
                      </svg>
                    </button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="summery mt-3">
          <h2 class="title m-0">{{ __('Order summary') }}</h2>
          <div class="mt-4">
            <div>
              <dl>
                <dt class="price">{{ __('Original price') }}</dt>
                <dd id="original">{{ $cart->show_subtotal() }}</dd>
              </dl>
              {{-- <dl>
              <dt>Savings</dt>
              <dd>-$299.00</dd>
            </dl> --}}
              {{-- <dl>
              <dt>Store Pickup</dt>
              <dd>$99</dd>
            </dl> --}}
              <dl>
                <dt>{{ __('Delivery') }}</dt>
                <dd id="delivery" data-price="00">$00</dd>
              </dl>
            </div>
            <dl class="total">
              <dt>{{ __('Total') }}</dt>
              <dd id="total">{{ $cart->show_total() }}</dd>
            </dl>
          </div>
        </div>

        <div class="end">
          <button name="action" value="shopping" type="submit" form="cart-form">Continue Shopping</button>
          <button name="action" value="checkout" type="submit" class="primary" form="cart-form">Proceed to
            Checkout</button>
        </div>
      </div>
    </div>
  </form>


@endsection

@section('scripts')
  <script src="{{ url('js/jquery.min.js') }}"></script>
  <script>
    const formatter = new Intl.NumberFormat('en-US', {
      style: "currency",
      currency: "{{ strtoupper(config('cart.currency_name')) }}",
    });

    const updatePrice = (priceEl, count) => {
      priceEl.text(formatter.format(+priceEl.data("price") * +count))
      let sum = 0;
      $(".product").each(function() {
        sum += Number($(this).data("price")) * Number($(this).find(".qty-input").val());
      });
      $("#original").text(formatter.format(sum));
      $("#total").text(formatter.format(sum + Number($("#delivery").data("price"))));
    }

    $('.qty-btn.add').click(function(e) {
      const input = $("#" + this.dataset.target)
      const value = input.val(+input.val() + 1)
      let priceEl = $("#product-price-" + input.data("id"));
      updatePrice(priceEl, value.val());
    });
    $('.qty-btn.sub').click(function(e) {
      const input = $("#" + this.dataset.target)
      if (+input.val() > 1) {
        const value = input.val(+input.val() - 1)
        let priceEl = $("#product-price-" + input.data("id"));
        // priceEl.text(formatter.format(+priceEl.data("price") * +input.val()))
        updatePrice(priceEl, value.val());
      }
    });

    $('.product .delete-product').click(function(e) {
      $("#product-" + $(this).data("id")).remove();
    });
  </script>
@endsection
