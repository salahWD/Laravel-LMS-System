@extends('layout')

@section('meta')
  <title>{{ __(config('app.name')) . ' | ' . __('Booking Appointment') }}</title>
@endsection

@section('styles')
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">

  @vite(['resources/css/home.css', 'resources/css/booking-appointment.css', 'resources/css/fullcalendar-main.css'])
@endsection

@section('content')
  <div class="appointment-page">
    <div class="container shadow">
      <section class="body-section">
        <h3>Confirmed!</h3>
        <p id="scheduler">You are scheduled with ACME Sales.</p>
      </section>
      <section class="description-section">
        <hgroup>
          <h3 id="event">Pricing Review</h3>
          <div class="icon-text-div">
            <img src="icons/clock.svg" alt="clock-icon">
            <h4 id="duration">15 min</h4>
          </div><br>
          <div class="icon-text-div">
            <img src="icons/calendar (1).svg" alt="calendar-icon">
            <h4 id="event-time-stamp">9:00am - 9:15am, Monday, July 13, 2020</h4>
          </div>
        </hgroup>
      </section>
    </div>
  </div>
@endsection

@section('scripts')
  <script src="{{ url('js/fullcalendar-main.js') }}"></script>
  @vite(['public/js/booking-appointment.js'])
@endsection
