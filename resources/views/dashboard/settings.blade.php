@extends('dashboard.layout')

@section('meta')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @php $page_title = config("app.name") . " | " . __('Dashboard') @endphp
@endsection

@section('styles')
  @vite('resources/css/dashboard/style.css')
@endsection

@section('content')
  <div class="card card-body border-0 shadow my-4">
    <h2 class="h5 mb-4">{{ __('Dashboard Settings') }}</h2>
    <hr>
    <form method="POST">
      @csrf
      <div class="row">
        {{-- <div class="col-md-6 mb-3">
            <div class="form-group">
              <label for="row-count">{{ __('tables row count') }}</label>
              <input class="form-control" id="row-count" type="number" placeholder="15 row per page" required>
            </div>
          </div> --}}
        <div class="col-md-6 mb-3">
          <div>
            <div class="form-group">
              <div class="form-check form-switch">
                <input class="form-check-input" name="shop_section" id="shop" type="checkbox"
                  @if (config('settings.shop_status')) checked @endif>
                <label class="form-check-label" for="shop">{{ __('Shop Section:') }}</label>
              </div>
            </div>
            <div class="form-group">
              <label for="table-row-count">tables row count</label>
              <input class="form-control" id="table-row-count" type="number" name="tables_row_count"
                placeholder="Enter the count of rows in the managing tables" required min="0" max="100"
                value="{{ config('settings.tables_row_count') }}">
            </div>
            {{-- <hr>
            <div class="form-group">
              <label>Header Links</label>
              @foreach (['Home', 'Articles', 'Courses', 'Shop'] as $i => $link)
                <div class="input-group mb-2">
                  <input type="text" value="{{ __($link) }}" class="form-control" disabled>
                </div>
              @endforeach
              <div class="accordion" id="accordion-{{ 1 }}">
                <div class="accordion-item">
                  <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                      data-bs-target="#collapse-{{ 0 }}" aria-expanded="true"
                      aria-controls="collapse-{{ 0 }}">
                      Does my subscription automatically renew?
                    </button>
                  </h2>
                  <div id="collapse-{{ 0 }}" class="accordion-collapse collapse show"
                    aria-labelledby="headingOne" data-bs-parent="#accordion-{{ 1 }}">
                    <div class="accordion-body">
                      At Themesberg, our mission has always been focused on bringing openness and transparency to the
                      design process. We've always believed that by providing a space where designers can share ongoing
                      work not only empowers them to make better products, it also helps them grow. We're proud to be a
                      part of creating a more open culture and to continue building a product that supports this vision.
                    </div>
                  </div>
                </div>
              </div>
              <div class="input-group mb-2">
                <input type="text" value="{{ __('Contact') }}" class="form-control" disabled>
              </div>
            </div> --}}
          </div>
        </div>
      </div>
      <div class="mt-3">
        <button class="btn btn-gray-800 mt-2 animate-up-2" type="submit">Save all</button>
      </div>
    </form>
  </div>
@endsection
