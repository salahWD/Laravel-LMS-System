@extends('dashboard.layout')

@section('meta')
  @php $page_title = config("app.name") . " | " . __('Edit Product') @endphp
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('styles')
  @vite('resources/css/dashboard/style.css')
@endsection

@section('content')

  <div class="container mt-4">
    <form method="POST" id="product-form"
      action="{{ isset($product->id) ? route('product_edit', $product->id) : route('product_create') }}"
      enctype="multipart/form-data">
      @csrf
      <div class="row">
        <div class="col-12 col-xl-7">
          <div class="card card-body border-0 shadow mb-4">
            {{-- <div style="@if (isset($product->id) && !$product->is_affiliate()) display: none @endif" id="link-section">
              <label for="product_link">
                <h2 class="h5 mb-4">{{ __('Import Product') }}</h2>
              </label>
              <div class="mb-3">
                <input type="hidden" id="productId" name="product_id">
                <input type="url" class="form-control @error('product_link') is-invalid @enderror" id="product_link"
                  name="product_link" data-product_id="{{ $product->product_id }}" type="url"
                  value="{{ old('product_link') ?? $product->product_link() }}"
                  placeholder="{{ __('e.g aliexpress, banggood link') }}">
                @error('product_link')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <hr>
            </div> --}}
            <div class="mb-3" id="type-section">
              <h2 class="h5 mb-4">{{ __('Product Type') }}</h2>
              <div class="form-check">
                <input class="form-check-input @error('product_type') is-invalid @enderror" type="radio"
                  name="product_type" id="dropshipping" value="1"
                  @if ($product->type != null && $product->type == 1) checked @elseif($product->type == null) checked @endif>
                <label class="form-check-label" for="dropshipping">
                  {{ __('dropshipping') }}
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input @error('product_type') is-invalid @enderror" type="radio"
                  name="product_type" id="affiliate" value="2" @if ($product->type != null && $product->type == 2) checked @endif>
                <label class="form-check-label" for="affiliate">
                  {{ __('affiliate') }}
                </label>
                @error('product_type')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <hr>
            <div class="mb-3" id="affiliate-section">
              <div class="mb-3">
                <label for="affiliate-link">{{ __('Affiliate Link') }}</label>
                <input class="form-control @error('affiliate_link') is-invalid @enderror" id="affiliate-link"
                  name="affiliate_link" type="text"
                  value="{{ old('affiliate_link') ?? ($product->get_link() !== null ? $product->get_link() : '') }}"
                  placeholder="{{ __('product link from affiliate store') }}">
                @error('affiliate_link')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <hr>
            <h2 class="h5 mb-4">{{ __('Product information') }}</h2>
            @if ($errors->any())
              <div class="alert alert-danger">
                <ul>
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
            <div class="mb-3">
              <label for="title">{{ __('Title') }}</label>
              <input class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                type="text" value="{{ old('title') ?? (isset($product->id) ? $product->title : '') }}"
                placeholder="{{ __('product title') }}">
              @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-3">
              <label for="desc">{{ __('Description') }}</label>
              <textarea class="form-control @error('description') is-invalid @enderror" id="desc" style="min-height: 80px;"
                name="description" placeholder="{{ __('product overview') }}">{{ old('description') ?? (isset($product->id) ? $product->description : '') }}</textarea>
              @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-3">
              <label for="cat">{{ __('Category') }}</label>
              <select id="cat" name="category" class="form-select @error('category') is-invalid @enderror">
                <option value="">{{ __('-- select a category --') }}</option>
                @foreach ($categories as $category)
                  <option @if (isset($product->id) && $category->id == $product->category_id) selected @endif value="{{ $category->id }}">
                    {{ $category->title ?? 'Unknown' }}</option>
                @endforeach
              </select>
              @error('category')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="row align-items-center" style="@if (isset($product->id) && $product->is_affiliate()) display: none @endif"
              id="price-section">
              <div class="col-sm-6 mb-3">
                <label for="price">{{ __('Price') }}</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">$</span>
                  </div>
                  <input class="form-control @error('price') is-invalid @enderror" id="price" name="price"
                    type="number" step="0.01" min="0"
                    value="{{ old('price') ?? (isset($product->id) ? $product->price : '') }}"
                    placeholder="{{ __('price of the product') }}">
                </div>
                @error('price')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-sm-6 mb-3">
                <label for="stock">{{ __('Stock') }}</label>
                <input class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock"
                  type="number" min="0"
                  value="{{ old('stock') ?? (isset($product->id) ? $product->stock : '') }}"
                  placeholder="{{ __('amount of stock') }}">
                @error('stock')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            <hr>
            <button class="btn btn-gray-800 mt-2 animate-up-2" type="submit">{{ __('Save all') }}</button>
          </div>
        </div>
        <div class="col-12 col-xl-5">
          <div class="row">
            <div class="col-12 mb-4">
              <div class="card shadow border-0 text-center p-0">
                <div class="card-body pb-5">
                  <p class="lead mb-0 mt-2" style="font-size: 1rem;">{{ __('max size 2MB') }}</p>
                  <img src="{{ $product->main_image_url() }}" id="productImagePreview"
                    class="rounded mx-auto m-0 article-thumbnail" alt="{{ __('product Image') }}">
                  <div id="product-images-preview" class="product-preview mt-3">
                    @if (isset($product->id) && $product->get_images() != null)
                      @foreach ($product->get_images() as $i => $img)
                        <div class="product-img rounded">
                          <input type="hidden" class="d-none"
                            @if ($i == 0) name="old_images[0]" @else name="old_images[]" @endif
                            value="{{ $img }}">
                          <img src="{{ $img }}" alt="{{ $img }}">
                          <div class="btns">
                            <button tabindex="-1" type="button" class="btn btn-sm btn-success"><i
                                class="fa fa-image"></i></button>
                            <button tabindex="-1" type="button" class="btn btn-sm btn-danger"><i
                                class="fa fa-trash"></i></button>
                          </div>
                        </div>
                      @endforeach
                    @endif
                  </div>
                  <hr>
                  <input type="file" class="d-none" name="images[]" id="productImage" multiple>
                  <button type="button" onclick="document.getElementById('productImage').click()"
                    class="btn btn-sm btn-warning d-inline-flex mx-3 align-items-center">
                    <svg class="w-22px" data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor"
                      viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z">
                      </path>
                      <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z"></path>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>

@endsection

@section('jslibs')
  @if (Session::has('product-saved') && Session::get('product-saved'))
    <script src="{{ url('libs/dashboard/sweetalert2.all.min.js') }}"></script>
  @endif
  <script src="{{ url('js/product-images.js') }}"></script>
@endsection

@section('scripts')
  <script>
    let typeSec = $("#type-section");
    let priceSec = $("#price-section");
    let proxyUrl = "{{ route('product_proxy') }}";

    $("#dropshipping").on("input", function() {
      $(stock).prop('disabled', false);
      $(price).prop('disabled', false);
      priceSec.slideDown();
    })
    $("#affiliate").on("input", function() {
      $(stock).prop('disabled', true);
      $(price).prop('disabled', true);
      priceSec.slideUp();
    })

    @if (Session::has('product-saved') && Session::get('product-saved'))
      Swal.fire({
        title: "Product Saved Successfully",
        customClass: {
          confirmButton: 'btn btn-success me-4',
        },
        buttonsStyling: false,
        inputAttributes: {
          autocapitalize: "off"
        },
        showCancelButton: false,
        confirmButtonText: "ok",
        showLoaderOnConfirm: false,
      })
    @endif
  </script>
@endsection
