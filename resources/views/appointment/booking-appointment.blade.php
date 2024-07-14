@extends('layout')

@section('meta')
  <title>{{ __(config('app.name')) . ' | ' . __('Booking Appointment') }}</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('styles')
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">

  <!-- STYLES -->
  @vite(['resources/css/home.css', 'resources/css/fullcalendar-main.css', 'resources/css/booking-appointment.css'])
  @if ($event->price != null && $event->price > 0)
    <script src="https://js.stripe.com/v3/"></script>
  @endif
@endsection

@section('content')
  <div class="appointment-page">
    @if ($errors->any())
      <div class="container p-3">
        <div class="mb-0 alert alert-danger rounded">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif
    <div class="container shadow mt-5">
      <div class="pages" id="pages">
        <div class="subpage booking-page active">
          <section class="description-section">
            <hgroup>
              <a style="width: fit-content" class="d-block"
                href="{{ route('user_appointments', $event->author->username) }}">
                <img class="rounded-circle object-fit-cover d-block mb-2" src="{{ $event->author->image_url() }}"
                  style="max-width: 80px;" alt="{{ $event->author->fullname() }}">
                <h4 class="mb-1" id="scheduler">{{ $event->author->fullname() }}</h4>
              </a>
              <h2 id="event">{{ $event->title }}</h2>
              <div class="icon-text-div">
                <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 0 512 512" width="20">
                  <g>
                    <path
                      d="m256 0c-141.164062 0-256 114.835938-256 256s114.835938 256 256 256 256-114.835938 256-256-114.835938-256-256-256zm121.75 388.414062c-4.160156 4.160157-9.621094 6.253907-15.082031 6.253907-5.460938 0-10.925781-2.09375-15.082031-6.253907l-106.667969-106.664062c-4.011719-3.988281-6.25-9.410156-6.25-15.082031v-138.667969c0-11.796875 9.554687-21.332031 21.332031-21.332031s21.332031 9.535156 21.332031 21.332031v129.835938l100.417969 100.414062c8.339844 8.34375 8.339844 21.824219 0 30.164062zm0 0"
                      data-original="#000000" class="active-path" data-old_color="#000000" fill="#959699" />
                  </g>
                </svg>
                <h4 class="ms-1" id="duration">{{ $event->show_duration() }}</h4>
              </div>
            </hgroup>
            <p id="description">{{ $event->description }}</p>
          </section>
          <div class="divider"></div>
          <section id="calendar-section" class="body-section">
            <h3>Select a Date & Time</h3>
            <div id="schedule-div">
              <div id="available-times-div">
                <!-- Available times -->
              </div>
              <div id="calendar"></div>
            </div>
          </section>
        </div>
        <div class="subpage confirm-page">
          <section class="description-section">
            <button class="back-btn" id="back-to-booking">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                id="Capa_1" x="0px" y="0px" viewBox="0 0 447.243 447.243"
                style="enable-background:new 0 0 447.243 447.243;" xml:space="preserve" width="20" height="20">
                <g>
                  <g>
                    <g>
                      <path
                        d="M420.361,192.229c-1.83-0.297-3.682-0.434-5.535-0.41H99.305l6.88-3.2c6.725-3.183,12.843-7.515,18.08-12.8l88.48-88.48    c11.653-11.124,13.611-29.019,4.64-42.4c-10.441-14.259-30.464-17.355-44.724-6.914c-1.152,0.844-2.247,1.764-3.276,2.754    l-160,160C-3.119,213.269-3.13,233.53,9.36,246.034c0.008,0.008,0.017,0.017,0.025,0.025l160,160    c12.514,12.479,32.775,12.451,45.255-0.063c0.982-0.985,1.899-2.033,2.745-3.137c8.971-13.381,7.013-31.276-4.64-42.4    l-88.32-88.64c-4.695-4.7-10.093-8.641-16-11.68l-9.6-4.32h314.24c16.347,0.607,30.689-10.812,33.76-26.88    C449.654,211.494,437.806,195.059,420.361,192.229z"
                        data-original="#000000" class="active-path" data-old_color="#000000" fill="#12A3FF" />
                    </g>
                  </g>
                </g>
              </svg>
            </button>
            <hgroup>
              <h4>{{ $event->author->fullname() }}</h4>
              <h2 id="event">Pricing Review</h2>
              <div class="icon-text-div">
                <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 0 512 512" width="20">
                  <g>
                    <path
                      d="m256 0c-141.164062 0-256 114.835938-256 256s114.835938 256 256 256 256-114.835938 256-256-114.835938-256-256-256zm121.75 388.414062c-4.160156 4.160157-9.621094 6.253907-15.082031 6.253907-5.460938 0-10.925781-2.09375-15.082031-6.253907l-106.667969-106.664062c-4.011719-3.988281-6.25-9.410156-6.25-15.082031v-138.667969c0-11.796875 9.554687-21.332031 21.332031-21.332031s21.332031 9.535156 21.332031 21.332031v129.835938l100.417969 100.414062c8.339844 8.34375 8.339844 21.824219 0 30.164062zm0 0"
                      data-original="#000000" class="active-path" data-old_color="#000000" fill="#959699" />
                  </g>
                </svg>
                <h4>{{ $event->show_duration() }}</h4>
              </div><br>
              <div class="icon-text-div">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" id="Layer_1" enable-background="new 0 0 512 512"
                  height="512px" viewBox="0 0 512 512" width="512px">
                  <g>
                    <g>
                      <path
                        d="m446 40h-46v-24c0-8.836-7.163-16-16-16s-16 7.164-16 16v24h-224v-24c0-8.836-7.163-16-16-16s-16 7.164-16 16v24h-46c-36.393 0-66 29.607-66 66v340c0 36.393 29.607 66 66 66h380c36.393 0 66-29.607 66-66v-340c0-36.393-29.607-66-66-66zm34 406c0 18.778-15.222 34-34 34h-380c-18.778 0-34-15.222-34-34v-265c0-2.761 2.239-5 5-5h438c2.761 0 5 2.239 5 5z"
                        data-original="#000000" class="active-path" data-old_color="#000000" fill="#3CB371" />
                    </g>
                  </g>
                </svg>
                <h4 id="event-time-stamp">9:00am - 9:15am, Monday, July 13, 2020</h4>
              </div>
            </hgroup>
          </section>
          <div class="divider"></div>
          <section id="register-section" class="body-section">
            <div id="error-message"></div>
            <form id="payment-form" style="padding-top: 10px">
              @csrf
              <h3 class="title mt-3">Payment</h3>
              <div id="payment-element">
                <!--Stripe.js injects the Payment Element-->
              </div>
              <button id="submitBtn" class="btn btn-primary w-100 mt-4">
                <div class="spinner hidden" id="spinner"></div>
                <span id="button-text">Pay now</span>
              </button>
              <div id="payment-message" class="hidden"></div>
            </form>
            {{-- <form action="{{ route('appointment_booking_store', $event->id) }}" method="POST">
              @sccrf
              <h3>Enter Details</h3>
              <label for="name">Name</label>
              <input type="text" name="name" id="name" required>
              <label for="email">Email</label>
              <input type="email" name="email" id="email" required>
              <button id="submit-btn" type="submit">Schedule Event</button>
            </form> --}}
          </section>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script src="{{ url('js/jquery.min.js') }}"></script>
  <script>
    var AppointmentsUrl = "{{ route('appointment_booking_store', $event->url) }}";
    var allAvailableTimes = JSON.parse(`{!! $settings !!}`);
    var AppointmentDuration = {{ $event->duration }};
    var AppointmentBufferZone = {{ $event->buffer_zone }};
    const EventTimezone = "{{ $event->timezone }}";
    @if ($event->price != null && $event->price > 0)
      const clientSecretKey = "{{ $intent }}";
      const stripeKey = "{{ config('services.stripe.key') }}";
      const clientSecret = "{{ $intentId }}";
      const successUrl = "{{ route('profile.meetings') }}";
    @endif
    const has_price = "{{ $event->price != null && $event->price > 0 }}";
  </script>
  <script src="{{ url('js/fullcalendar-main.js') }}"></script>
  @vite(['public/js/booking-appointment.js'])
@endsection
