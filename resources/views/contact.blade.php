@extends('layout')

@section('meta')
  <title>{{ __(config('app.name')) . ' | ' . __('Contact') }} {{ $email }}</title>
@endsection

@section('styles')
  @vite(['resources/css/contact.css'])
@endsection

@section('content')
  <div class="container">
    <div class="row justify-content-center">
      <img src="/images/gmail-logo.png" alt="gmail logo" />
      {{ $email }}
      <a href="mailto:salahb170@gmail.com" target="_blank">Go To Gmail</a>
    </div>
  </div>
@endsection

@section('scripts')
  <!-- JAVA SCRIPTS -->
  <script src="{{ url('js/jquery.min.js') }}"></script>
  <script src="{{ url('js/popper.min.js') }}"></script>
  <script src="{{ url('js/bootstrap.min.js') }}"></script>
  <script src="{{ url('js/slick.min.js') }}"></script>
  <script src="{{ url('js/jquery.sticky-sidebar.min.js') }}"></script>
@endsection
