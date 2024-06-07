@extends('dashboard.layout')

@section('meta')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @php $page_title = config("app.name") . " | " . __('Dashboard') @endphp
@endsection

@section('styles')
  @vite("resources/css/dashboard/style.css")
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
                  <input class="form-check-input" name="certification_section" id="certifications" type="checkbox" required>
                  <label class="form-check-label" for="certifications">{{ __('Certificates Section:') }}</label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="mt-3">
            <button class="btn btn-gray-800 mt-2 animate-up-2" type="submit">Save all</button>
        </div>
      </form>
  </div>
@endsection
