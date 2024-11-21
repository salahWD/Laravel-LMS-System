@extends('layout')

@section('meta')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ __(config('app.name')) . ' | ' . $product->title }}</title>
@endsection

@section('styles')
  <link rel="stylesheet" href="{{ url('libs/owl/owl.carousel.min.css') }}">
  <link rel="stylesheet" href="{{ url('libs/hizoom/hizoom.min.css') }}">
  <style>
    :root {
      --hot-deal-slider-lbtn: url("{{ url('images/hot-deal-slider-lbtn.png') }}");
      --hot-deal-slider-rbtn: url("{{ url('images/hot-deal-slider-rbtn.png') }}");
    }
  </style>
  @vite(['resources/css/single-product.css', 'resources/css/product.css'])
@endsection

@section('content')
  <div class="container mt-5">
    <div class="row">
      <div class="col-1 d-md-none"></div>
      <div class="col-10 col-md-5">
        <div class='hizoom product-preview-image'>
          <img src='{{ $product->main_image_url() }}'>
        </div>
        @if ($product->get_images() != null)
          <div class="product-images">
            <div class="track">
              @foreach ($product->get_images() as $image)
                <img class="rounded border" src="{{ $image }}" alt="product image">
              @endforeach
            </div>
          </div>
        @endif
      </div>
      <div class="col-1 d-md-none"></div>
      <div class="col-12 col-md-7">
        <div class="single-product-overview">
          <h2>{{ $product->title }}</h2>
          {{-- <div class="shopify-product-rating">
            <span class="shopify-product-reviews-badge" data-id="6027649223"></span>
          </div> --}}
          <p class="desc">
            {{ $product->description }}
          </p>
          {{-- ====== features of the product ====== --}}
          {{-- <ul class="product-description mt-35 mb-35">
            <li>
              <span>Availability:</span>
              In Stock
            </li>
          </ul> --}}
          <div class="cart-options">
            <div class="mt-4">
              <div class="prices">
                <span class="price">{{ $product->show_long_price() }}</span>
                @if (!$product->has_old_price())
                  <s class="old-price">{{ $product->show_long_old_price() }}</s>
                @endif
              </div>
              <div class="quantity mt-2">
                <span class="btn btn-plus">
                  <i class="fa fa-plus"></i>
                </span>
                <input type="text" data-min="1" data-minalert="{{ __('minimum limit reached') }}"
                  data-max="{{ $product->max_order_quantity() ?? 500 }}"
                  data-maxalert="{{ __('maximum limit reached') }}" data-invalid="{{ __('enter valid quantity') }}"
                  id="quantity" value="1" />
                <span class="btn btn-minus">
                  <i class="fa fa-minus"></i>
                </span>
              </div>
              <button id="add-to-cart" class="btn btn-add-to-cart">{{ __('add to cart') }}</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <hr>
  </div>
  <div class="container">
    <div class="sub-pages">
      <ul class="nav nav-tabs ps-4">
        <li class="nav-item">
          <button data-page="details-page" class="nav-link active">Details</button>
        </li>
        <li class="nav-item">
          <button data-page="reviews-page" class="nav-link">Reviews</button>
        </li>
        <li class="nav-item">
          <button data-page="shipping-page" class="nav-link">Shipping Info</button>
        </li>
        <li class="nav-item">
          <button data-page="faq-page" class="nav-link">FAQ</button>
        </li>
      </ul>
      <div class="pages rounded">
        <div class="page details-page active" id="details-page">
          Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, nec sagittis sem lorem quis bibe dum auctor,
          nisi elit consequat ipsum, nec sagittis sem nibh id elit. Duis sed odio sit amet nibh vulputate cursus a nec
          sagittis sem sit amet mauris. Morbi accumsan ipsum velit. Nam nec tellus a odio tincidunt auctor a ornare odio.
          Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, nec sagittis sem lorem quis bibe dum auctor.
          5.5-inch (diagonal) LED-backlit widescreen Multi-Touch
          A8 chip with 64-bit architecture
          1080p HD video recording (30 fps or 60 fps)
          M8 motion coprocessor
          Full sRGB standard
          Slo-mo video (120 fps or 240 fps)
          Touch ID fingerprint identity sensor built into the Home button
        </div>
        <div class="page reviews-page" id="reviews-page">
          Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, nec sagittis sem lorem quis bibe dum auctor,
          nisi elit consequat ipsum, nec sagittis sem nibh id elit. Duis sed odio sit amet nibh vulputate cursus a nec
          sagittis sem sit amet mauris. Morbi accumsan ipsum velit. Nam nec tellus a odio tincidunt auctor a ornare odio.
          Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, nec sagittis sem lorem quis bibe dum auctor.
          5.5-inch (diagonal) LED-backlit widescreen Multi-Touch
          A8 chip with 64-bit architecture
          1080p HD video recording (30 fps or 60 fps)
          M8 motion coprocessor
          Full sRGB standard
          Slo-mo video (120 fps or 240 fps)
          Touch ID fingerprint identity sensor built into the Home button
        </div>
        <div class="page shipping-page" id="shipping-page">
          Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, nec sagittis sem lorem quis bibe dum auctor,
          nisi elit consequat ipsum, nec sagittis sem nibh id elit. Duis sed odio sit amet nibh vulputate cursus a nec
          sagittis sem sit amet mauris. Morbi accumsan ipsum velit. Nam nec tellus a odio tincidunt auctor a ornare odio.
          Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, nec sagittis sem lorem quis bibe dum auctor.
          5.5-inch (diagonal) LED-backlit widescreen Multi-Touch
          A8 chip with 64-bit architecture
          1080p HD video recording (30 fps or 60 fps)
          M8 motion coprocessor
          Full sRGB standard
          Slo-mo video (120 fps or 240 fps)
          Touch ID fingerprint identity sensor built into the Home button
        </div>
        <div class="page faq-page" id="faq-page">
          Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, nec sagittis sem lorem quis bibe dum auctor,
          nisi elit consequat ipsum, nec sagittis sem nibh id elit. Duis sed odio sit amet nibh vulputate cursus a nec
          sagittis sem sit amet mauris. Morbi accumsan ipsum velit. Nam nec tellus a odio tincidunt auctor a ornare odio.
          Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, nec sagittis sem lorem quis bibe dum auctor.
          5.5-inch (diagonal) LED-backlit widescreen Multi-Touch
          A8 chip with 64-bit architecture
          1080p HD video recording (30 fps or 60 fps)
          M8 motion coprocessor
          Full sRGB standard
          Slo-mo video (120 fps or 240 fps)
          Touch ID fingerprint identity sensor built into the Home button
        </div>
      </div>
    </div>
  </div>
  <div class="container mt-5">
    <div class="similar-products">
      <div class="owl-carousel similar-products-carousel products owl-theme owl-loaded" dir="ltr">
        @foreach ($similar_products as $product)
          <div class="xv-product product style shadow-around">
            <figure>
              <a href="{{ $product->get_link() }}">
                <div class="reveal">
                  @if ($product->get_images())
                    @foreach ($product->get_images(2) as $i => $image)
                      <img class="@if ($i == 0) owl-lazy xv-superimage @else hidden @endif"
                        src="{{ $image }}" alt="{{ $product->title }}"
                        @if ($i == 0) style="opacity: 1;" @endif>
                    @endforeach
                  @endif
                </div>
              </a>
            </figure>

            <div class="xv-product-content">
              <h3><a href="{{ $product->get_link() }}">{{ $product->title }}</a></h3>
              <div class="color-opt">{{ $product?->category?->title }}</div>
              <span class="xv-price"> {{ $product->show_price() }} </span>
              <div style="display:none;">
                <select name="id">
                  <option value="18772192327">Default Title - {{ $product->show_price() }}</option>
                </select>
              </div>
              <button type="submit" name="add" class="product-buy flytoQuickView"
                data-qv-tab="#qvt-cart">{{ __('Buy') }}</button>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  @include('shop.cart')
@endsection

@section('scripts')
  <script src="{{ url('js/jquery.min.js') }}"></script>
  <script src="{{ url('libs/owl/owl.carousel.min.js') }}"></script>
  <script src="{{ url('libs/hizoom/hizoom.min.js') }}"></script>
  {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> --}}
  <script>
    const cardUrl = "{{ route('add_product_cart', $product->id) }}";
  </script>
  <script src="{{ url('js/single-product.js') }}"></script>
@endsection
