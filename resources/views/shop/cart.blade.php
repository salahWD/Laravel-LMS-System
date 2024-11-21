<style>
  .cart-btns {
    position: fixed;
    top: 30%;
    left: 0;
    direction: ltr;
    z-index: 99;
  }

  .cart-btns .btn {
    position: relative;
  }

  .cart-btns .cart {
    border-radius: 0 8px 8px 0;
    color: white;
    background: #3d93c5;
    padding: 12px 10px;
    overflow: visible;
  }

  .cart-btns .cart i {
    font-size: 24px;
    margin-right: 4px;
  }

  .cart-btns .cart .badge {
    position: absolute;
    width: 22px;
    height: 22px;
    text-align: center;
    line-height: 22px;
    font-size: 10px;
    color: #fff;
    z-index: 20;
    right: -5px;
    top: -5px;
    background: #ff527f;
    line-height: 15px;
    border-radius: 50%;
    padding: 4px 5px;
  }
</style>
<div class="cart-btns">
  <a class="btn cart" href="{{ route('cart_show') }}">
    <i class="fa fa-shopping-cart" aria-hidden="true"></i>
    <div class="badge" id="cartItemsCount" data-count="{{ $cart_items_count }}">
      @if ($cart_items_count > 0)
        {{ $cart_items_count }}
      @endif
    </div>
  </a>
</div>
