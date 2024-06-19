@extends('dashboard.layout')

@section('meta')
  @php $page_title = config("app.name") . " | " . __('meetings') @endphp
@endsection

@section('styles')
  @vite(['resources/css/dashboard/style.css', 'resources/css/dashboard/meetings.css'])
@endsection

@section('content')
  <div class="container">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
      <div class="d-block mb-4 mb-md-0">
        <x-breadcrumb page="meetings" is-route=1 url="meetings_manage" />
        <h2 class="h4 text-capitalize">{{ __('my meetings') }}</h2>
        <p class="mb-0">{{ __('manage :all from one place.', ['all' => __('your meetings')]) }}</p>
      </div>
      <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('meeting_create') }}"
          class="btn btn-sm btn-gray-800 d-inline-flex align-items-center text-capitalize">
          <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
            </path>
          </svg>
          {{ __('New :item', ['item' => __('meeting')]) }}
        </a>
      </div>
    </div>
  </div>
  <div class="container mt-5">
    @if ($meetings->count() > 0)
      <div class="row">
        @foreach ($meetings as $meeting)
          <div class="col-md-4">
            <div class="card meeting bordered-card mb-3" style="--br-clr: {{ $meeting->color }}">
              <div class="card-body">
                <h5 class="card-title"><a href="{{ route('meeting_edit', $meeting->id) }}">{{ $meeting->title }}</a></h5>
                <p class="card-text">{{ $meeting->description }}</p>
                <a href="{{ route('meeting_show', $meeting->id) }}" class="btn btn-link">View booking
                  page</a>
                <div class="d-flex justify-content-between align-items-center mt-3">
                  <a class="btn btn-outline-primary" href="{{ route('meeting_edit', $meeting->id) }}">
                    Edit <i class="fa fa-edit"></i>
                  </a>
                  <button data-id="{{ $meeting->id }}" class="on-off-btn btn btn-outline-gray-800 text-capitalize">
                    Turn Off
                  </button>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div role="alert" class="custom-alert-1">
        <p class="font-bold text-capitalize" style="margin: 0px; font-weight: 700;">{{ __('warning') }}</p>
        <p style="margin: 0px;">{{ __('there is no :items to show', ['items' => __('meetings')]) }}.</p>
      </div>
    @endif
  </div>
@endsection

@section('scripts')
  <script>
    let btn = $('.on-off-btn');

    btn.each(function() {
      $(this).on("click", function() {
        $(this).toggleClass(['btn-outline-gray-800', 'btn-outline-gray-500'])
        if ($(this).hasClass("btn-outline-gray-800")) {
          $(this).text('{{ __('turn off') }}')
        } else {
          $(this).text('{{ __('turn on') }}')
        }
      });
    });
  </script>
@endsection
