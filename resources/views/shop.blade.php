@extends('layout')

@section('styles')
  <link rel="stylesheet" href="{{ url('libs/owl/owl.carousel.min.css') }}">
  <link rel="stylesheet" href="{{ url('libs/owl/owl.theme.default.min.css') }}">
  <style>
    :root {
      --poly-image: url("{{ url('images/poly.png') }}");
      --patern-image: url("{{ url('images/pattren.png') }}");
      --timer-bg: url("{{ url('images/timer-bg.jpg') }}");
      --btn-bg: url("{{ url('images/btn-bg.webp') }}");
      --hot-deal-slider-lbtn: url("{{ url('images/hot-deal-slider-lbtn.png') }}");
      --hot-deal-slider-rbtn: url("{{ url('images/hot-deal-slider-rbtn.png') }}");
    }
  </style>
  @vite(['resources/css/shop.css', 'resources/css/product.css'])
@endsection

@section('content')
  <section class="main-content mt-0">
    <div class="header-carousel owl-carousel">
      @php $colors = ["#6a5acd", "#000000", "#708090"] @endphp
      @foreach ($header_slides as $i => $slide)
        <div class="item" style="background-color: {{ $colors[$i] ?? '' }}">
          <div class="container-xl">
            <div class="content">
              <div class="row">
                <div class="hidden-xs hidden-sm col-md-6">
                  <img
                    src="https://planet-17.myshopify.com/cdn/shop/t/9/assets/slide_img_2.png?v=58646201550105743901460189156"
                    alt="">
                </div>
                <div class="col-md-6">
                  <div class="info">
                    <h3 class="title">get started with products</h3>
                    <p class="lead">Lorem ipsum dolor sit amet consectetur adipisicing elit. Saepe
                      culpa amet delectus.</p>
                    <div class="flex gap-2">
                      <a href="#" class="btn btn-primary py-2 px-3">{{ __('more info') }}</a>
                      <a href="#" class="btn btn-primary py-2 px-3">{{ __('shop now') }}</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </section>
  <section class="main-content">
    <div class="container">
      <div class="text-center">
        <h2 class="section-title">{{ __('new products') }}</h2>
      </div>
      <div class="row">
        <div class="col-xs-12 col-sm-3 hidden-xs">
          <div class="flex flex-col">
            <ul class="list-group">
              @if (count($categories) > 0)
                @foreach ($categories as $category)
                  <li class="list-group-item">
                    <a href="{{ $category->get_link() }}">
                      <span class="badge bg-primary rounded px-3 me-2">
                        {{ $category->products_count ?? '?' }}
                      </span>
                      {{ $category->title }}
                    </a>
                  </li>
                @endforeach
              @else
                <div class="m-0 alert border border-warning rounded alert-warning">
                  <h6 class="m-0">{{ __('no categories to show') }}</h6>
                </div>
              @endif
            </ul>
          </div>
        </div>
        <div class="col-xs-12 col-sm-9">
          <div class="owl-carousel products-carousel products owl-theme owl-loaded" data-prev="fa fa-angle-left"
            data-next="fa fa-angle-right">
            @foreach ($new_products as $product)
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

                  {{-- ======= Rating ======= --}}
                  {{-- <span class="review d-block">
                    <i class="fa fa-star" aria-hidden="true"></i>
                    <i class="fa fa-star" aria-hidden="true"></i>
                    <i class="fa fa-star" aria-hidden="true"></i>
                    <i class="fa fa-star-half" aria-hidden="true"></i>
                  </span> --}}

                  <span class="xv-price"> {{ $product->show_price() }} </span>
                  <div style="display:none;">
                    <select name="id">
                      <option value="18772192327">Default Title - {{ $product->show_price() }}
                      </option>
                    </select>
                  </div>
                  <a class="product-buy" href="{{ route('product_show', $product->id) }}">{{ __('Buy') }}</a>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    <div class="mt-4 mb-5 owl-carousel offers offers-slider m-auto owl-theme custom-nav-icon owl-loaded">
      @foreach ($offers as $offer)
        <div class="offer d-flex">
          <figure>
            <img
              src="//planet-17.myshopify.com/cdn/shop/products/z33_3a4076f1-94b2-4443-aa71-8fbf9f92f2a2_large.png?v=1459702297"
              alt="Canon EOS-1">
          </figure>
          <div class="saletimeout-content text-center">
            <h2><span>{{ $offer->title }}</span></h2>
            <span>{{ __('hot deal only') }} &nbsp;{{ $offer->show_price() }}</span>
            <div class="counter-wrapper" data-target="2016/8/20 23:00:00">
              <div class="hours">02<span>hour</span></div>
              <div class="minutes">15<span>min</span></div>
              <div class="seconds">04<span>sec</span></div>
            </div>
            <a href="{{ $product->get_link() }}">{{ __('Buy Now') }}</a>
          </div>
        </div>
      @endforeach
    </div>

    <div class="container">
      <div class="bestsales">
        <div class="text-center">
          <h2 class="section-title">{{ __('best salles') }}</h2>
        </div>
        @if ($new_products->count() > 0)
          <div class="owl-carousel bestsales-carousel products owl-theme owl-loaded" data-prev="fa fa-angle-left"
            data-next="fa fa-angle-right">
            @foreach ($new_products as $product)
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

                  {{-- ======= Rating ======= --}}
                  {{-- <span class="review d-block">
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star" aria-hidden="true"></i>
                                    <i class="fa fa-star-half" aria-hidden="true"></i>
                                  </span> --}}

                  <span class="xv-price"> {{ $product->show_price() }} </span>
                  <div style="display:none;">
                    <select name="id">
                      <option value="18772192327">Default Title - {{ $product->show_price() }}
                      </option>
                    </select>
                  </div>
                  <a class="product-buy" href="{{ route('product_show', $product->id) }}">{{ __('Buy') }}</a>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="container">
            <div class="m-0 alert border border-warning rounded alert-warning">
              <h4>there are no products to show !</h4>
            </div>
          </div>
        @endif
      </div>
      <div class="packages-section mt-5 pt-5">
        <div class="text-center">
          <h2 class="section-title">{{ __('recommended packages') }}</h2>
        </div>
        <div class="container">
          <div class="packages owl-carousel packages-slider m-auto owl-theme custom-nav-icon owl-loaded">
            @if ($packages->count() > 0)
              @foreach ($packages as $package)
                @if ($package->shift() != null)
                  <div class="package p-4">
                    <div class="row align-items-center">
                      <div class="col-xs-12 col-sm-6">
                        <div class="main-product"
                          style="--main-product-padding: @if (app()->getLocale() == 'ar') 0 0 0 1.5rem @else 0 1.5rem 0 0 @endif">
                          @php $product = $package->shift(); @endphp
                          <figure class="shadow">
                            <a href="{{ $product->get_link() }}">
                              <div class="reveal">
                                @if ($product->get_images())
                                  @foreach ($product->get_images(2) as $i => $image)
                                    <img
                                      class="@if ($i == 0) owl-lazy xv-superimage w-100 @else hidden @endif"
                                      src="{{ $image }}" alt="{{ $product->title }}"
                                      @if ($i == 0) style="opacity: 1;" @endif>
                                  @endforeach
                                @endif
                              </div>
                            </a>
                          </figure>
                        </div>
                      </div>
                      <div class="col-xs-12 col-sm-6">
                        <div class="row">
                          {{-- @if ($package->get_products_count() >= 4) --}}
                          @php $loops = 0 @endphp
                          @foreach ($package as $product)
                            @php $loops++ @endphp
                            @if ($loops <= 4)
                              @if ($package->count() >= 3)
                                <div class="col-xs-12 col-sm-6 mt-not-second">
                                  <figure class="shadow">
                                    <a href="{{ $product->get_link() }}">
                                      <div class="reveal">
                                        @if ($product->get_images())
                                          @foreach ($product->get_images(2) as $i => $image)
                                            <img
                                              class="@if ($i == 0) owl-lazy xv-superimage @else hidden @endif"
                                              src="{{ $image }}" alt="{{ $product->title }}"
                                              @if ($i == 0) style="opacity: 1;" @endif>
                                          @endforeach
                                        @endif
                                      </div>
                                    </a>
                                  </figure>
                                </div>
                              @elseif($package->count() == 2)
                                <div class="col-xs-12 mt-not-first">
                                  <figure class="shadow">
                                    <a href="{{ $product->get_link() }}">
                                      <div class="reveal">
                                        @if ($product->get_images())
                                          @foreach ($product->get_images(2) as $i => $image)
                                            <img
                                              class="@if ($i == 0) owl-lazy xv-superimage @else hidden @endif"
                                              src="{{ $image }}" alt="{{ $product->title }}"
                                              @if ($i == 0) style="opacity: 1;" @endif>
                                          @endforeach
                                        @endif
                                      </div>
                                    </a>
                                  </figure>
                                </div>
                              @else
                                <div class="col-xs-12">
                                  <div class="main-product"
                                    style="--main-product-padding: @if (app()->getLocale() == 'ar') 0 0 0 1.5rem @else 0 1.5rem 0 0 @endif">
                                    <figure class="shadow">
                                      <a href="{{ $product->get_link() }}">
                                        <div class="reveal">
                                          @if ($product->get_images())
                                            @foreach ($product->get_images(2) as $i => $image)
                                              <img
                                                class="@if ($i == 0) owl-lazy xv-superimage  w-100 @else hidden @endif"
                                                src="{{ $image }}" alt="{{ $product->title }}"
                                                @if ($i == 0) style="opacity: 1;" @endif>
                                            @endforeach
                                          @endif
                                        </div>
                                      </a>
                                    </figure>
                                  </div>
                                </div>
                              @endif
                            @endif
                          @endforeach
                        </div>
                      </div>
                    </div>
                  </div>
                @else
                  <div class="container">
                    <div class="m-0 alert border border-warning rounded alert-warning">
                      <h4>there are no products to show !</h4>
                    </div>
                  </div>
                @endif
              @endforeach
            @else
              <div class="container">
                <div class="m-0 alert border border-warning rounded alert-warning">
                  <h4>there are no products to show !</h4>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </section>

  @include('shop.cart')
@endsection

@section('scripts')
  <script src="{{ url('js/jquery.min.js') }}"></script>
  <script src="{{ url('libs/owl/owl.carousel.min.js') }}"></script>
  <script>
    $(document).ready(function() {
      $(".header-carousel").owlCarousel({
        loop: true,
        lazyLoad: true,
        margin: 15,
        nav: false,
        dots: true,
        center: true,
        singleItem: true,
        stagePadding: 0,
        smartSpeed: 450,
        items: 1,
        @if (app()->getLocale() == 'ar')
          rtl: true,
        @endif
        responsive: {
          0: {
            items: 1
          },
          600: {
            items: 1
          },
          1e3: {
            items: 1
          },
        },
      })
      $(".products-carousel").owlCarousel({
        loop: true,
        nav: true,
        lazyLoad: true,
        margin: 15,
        items: 3,
        navText: [
          "<i class='fa fa-angle-left'></i>",
          "<i class='fa fa-angle-right'></i>"
        ],
        responsive: {
          0: {
            items: 2
          },
          600: {
            items: 2
          },
          960: {
            items: 3
          },
          1200: {
            items: 3
          }
        }
      })
      $(".offers").owlCarousel({
        loop: true,
        lazyLoad: true,
        margin: 15,
        nav: true,
        dots: false,
        center: true,
        singleItem: true,
        stagePadding: 0,
        smartSpeed: 450,
        items: 1,
        @if (app()->getLocale() == 'ar')
          rtl: true,
        @endif
        navText: ["<i class='hot-deal-btnl'></i>", "<i class='hot-deal-btnr'></i>"]
      })
      $(".bestsales-carousel").owlCarousel({
        loop: true,
        nav: false,
        lazyLoad: true,
        margin: 15,
        items: 4,
        navText: [
          "<i class='fa fa-angle-left'></i>",
          "<i class='fa fa-angle-right'></i>"
        ],
        responsive: {
          0: {
            items: 2
          },
          600: {
            items: 2
          },
          960: {
            items: 4
          },
          1200: {
            items: 4
          }
        }
      })
      $(".packages-slider").owlCarousel({
        loop: true,
        lazyLoad: true,
        margin: 15,
        nav: true,
        dots: false,
        center: true,
        singleItem: true,
        stagePadding: 0,
        smartSpeed: 450,
        items: 1,
        @if (app()->getLocale() == 'ar')
          rtl: true,
        @endif
        navText: ["<i class='hot-deal-btnl'></i>", "<i class='hot-deal-btnr'></i>"]
      })
    });
  </script>
@endsection
