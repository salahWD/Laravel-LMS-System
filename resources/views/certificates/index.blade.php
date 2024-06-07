@extends('layout')

@section('meta')
  <title>{{ __(config("app.name")) . " | " . __('Certificate') }}</title>
@endsection

@section('styles')
@endsection

@section('content')

  <div class="container pt-4">
    @if (count($certificates) > 0)
      @foreach($certificates as $certi)
        <a href="{{ route("certificate_show", $certi->id) }}">{{ $certi->title }}</a>
      @endforeach
    @else
    @endif
  </div>

@endsection

@section('scripts')
  <!-- JAVA SCRIPTS -->
  <script src="{{ url("js/jquery.min.js") }}"></script>
  <script src="{{ url("js/popper.min.js") }}"></script>
  <script src="{{ url("js/bootstrap.min.js") }}"></script>
  <script src="{{ url("js/slick.min.js") }}"></script>
  <script src="{{ url("js/jquery.sticky-sidebar.min.js") }}"></script>
  <script src="{{ url("js/custom.js") }}"></script>
  <script>
  </script>
@endsection
