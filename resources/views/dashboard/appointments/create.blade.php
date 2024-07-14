@extends('dashboard.layout')

@section('meta')
  @php $page_title = config("app.name") . " | " . __('appointments') @endphp
@endsection

@section('styles')
  @vite(['resources/css/dashboard/style.css', 'resources/css/dashboard/appointment-dashboard.css'])
@endsection

@section('content')
  <div class="container">
    <div id="booking-type-editor">
      <form method="POST">
        @csrf
        <fieldset>
          <div class="row justify-content-center">
            <div class="col-md-12 col-lg-9">
              <div class="bg-white py-3 px-3 rounded shadow">
                <div class="d-flex flex-wrap flex-column gap-4 align-items-center mb-4">
                  <h1 class="h3 fw-bold d-inline mb-0">
                    @if (isset($appointment))
                      {{ __('Edit booking type') }}
                    @else
                      {{ __('Create new booking type') }}
                    @endif
                  </h1>
                  @if ($errors->any())
                    <div class="alert alert-danger">
                      <ul>
                        @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                        @endforeach
                      </ul>
                    </div>
                  @endif
                </div>
                <div class="mb-2 pb-3">
                  <h2 class="mb-0 h6">
                    <label for="title" class="form-label fw-bold">Title:</label>
                  </h2>
                  <input type="text" name="title" id="title" class="form-control"
                    value="{{ null !== old('title') ? old('title') : (isset($appointment) ? $appointment->title : '') }}"
                    required>
                </div>
                <div class="mb-2 pb-3">
                  <h2 class="mb-0 h6">
                    <label for="url_slug" class="form-label fw-bold">URL:</label>
                  </h2>
                  <div class="input-group input-group-responsive">
                    <span class="input-group-text">{{ config('app.url') }}/appointments/</span>
                    <input type="text" id="url_slug" name="url_slug" class="form-control"
                      value="{{ null !== old('url_slug') ? old('url_slug') : (isset($appointment) ? $appointment->url : '') }}"required>
                  </div>
                </div>
                <div class="mb-2 pb-3">
                  <div class="mb-0 h6">
                    <label for="description" class="fw-bold form-label">Description:</label>
                  </div>
                  <textarea class="form-control" rows="3" name="description">{{ null !== old('description') ? old('description') : (isset($appointment) ? $appointment->description : '') }}</textarea>
                </div>
                <div class="d-flex flex-wrap">
                  <div class="row w-100">
                    <div class="mb-4 pb-2 col-12 col-md-6">
                      <h2 class="mb-0 h6">
                        <label for="duration_minutes" class="form-label fw-bold">Duration:</label>
                      </h2>
                      <div class="d-block">
                        <input type="number" id="duration_minutes" name="duration_minutes"
                          class="form-control d-inline me-2" min="1" style="max-width: 120px;"
                          value="{{ null !== old('duration_minutes') ? old('duration_minutes') : (isset($appointment) ? $appointment->duration : '') }}">
                        <div class="d-inline">minutes</div>
                      </div>
                    </div>
                    <div class="mb-4 pb-2 col-12 col-md-6">
                      <h2 class="mb-0 h6">
                        <label for="price" class="form-label fw-bold">Price:</label>
                      </h2>
                      <div class="input-group input-group-responsive" style="width: fit-content">
                        <span class="input-group-text">{{ config('cart.currency') }}</span>
                        <input type="number" id="price" name="price" class="form-control"
                          value="{{ null !== old('price') ? old('price') : (isset($appointment) ? $appointment->price : '') }}">
                      </div>
                    </div>
                  </div>
                </div>
                {{-- <div class="mb-4 pb-3">
                  <h2 class="mb-3 h6">
                    <label class="form-label fw-bold mb-0">When are you available for this
                      booking?</label>
                  </h2>
                  <div class="rounded-3 border d-flex flex-wrap align-items-start justify-content-center p-3">
                    <div class="col-12 col-sm-6 p-0 p-sm-4">
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="availability" id="availabilityWeekly"
                          value="weekly" checked="">
                        <label class="form-check-label mb-1" for="availabilityWeekly">Weekly</label>
                        <p class="small text-secondary mb-0">You’re available one or more times
                          during
                          the
                          week,
                          every
                          week.</p>
                      </div>
                    </div>
                    <div class="pb-2 w-100 d-sm-none">

                    </div>
                    <div class="col-12 col-sm-6 p-0 p-sm-4">
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="availability" id="availabilitySpecificDates"
                          value="specificDates">
                        <label class="form-check-label mb-1" for="availabilitySpecificDates">Specific
                          dates</label>
                        <p class="small text-secondary mb-0">You’re only available on specific
                          dates.
                        </p>
                      </div>
                    </div>
                  </div>
                </div> --}}
                <div class="mb-4 pb-3">
                  <label class="form-check-label" for="timezone">Your Timezone</label>
                  <select class="form-select" name="timezone" id="timezone"
                    data-hasTimezone={{ isset($appointment) && $appointment->timezone != null ? 'true' : 'false' }}>
                    @foreach ($timezonelist as $timezone)
                      <option
                        value="{{ $timezone }}"{{ $timezone == old('timezone') ? ' selected' : (isset($appointment) && $appointment->timezone == $timezone ? ' selected' : '') }}>
                        {{ $timezone }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="mb-4 pb-3">
                  <h3 class="mb-3 h6 fw-normal">Define your weekly availability below:</h3>
                  <div class="card hide-last-border">
                    @foreach (['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day_key => $day)
                      <div class="p-3 border-bottom day-element" data-day="{{ $day_key }}">
                        <div class="row">
                          <div class="col-md-3 mb-3 mb-md-0">
                            <div class="form-check my-md-2">
                              <input class="day-available-check form-check-input" type="checkbox"
                                name="available_days[{{ $day_key }}][status]" id="{{ $day }}-available"
                                @if (isset($available) && in_array($day_key, collect($available)->pluck('key')->toArray())) checked @elseif (!in_array($day, ['Sunday', 'Saturday'])) checked @endif>
                              <label class="form-check-label"
                                for="{{ $day }}-available">{{ $day }}</label>
                            </div>
                          </div>
                          <div class="col-md-9">
                            <div class="unavailable text-muted my-md-2 d-none fst-italic">Unavailable</div>
                            <div class="disable-if-no-day d-flex flex-wrap flex-md-nowrap align-items-end">
                              <div class="time-windows">
                              </div>
                              <div class="ms-md-4 mt-2 mt-sm-3 mt-md-0 col text-end">
                                <button
                                  class="add-window-btn btn btn-link btn-sm text-decoration-none text-nowrap px-0 lh-sm my-1"
                                  type="button">+ Add window</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
                <div class="mb-3">
                  <div class="form-check">
                    <input class="form-check-input" id="overrides" type="checkbox" name="excluded_days[status]"
                      @if (isset($excluded) && $excluded != null && count($excluded) > 0) checked @endif>
                    <h2 class="mb-0 h6 lh-base">
                      <label class="form-check-label fw-normal" for="overrides">
                        Add unavailable dates
                      </label>
                    </h2>
                  </div>
                  <p class="small text-secondary mb-0">Define specific dates that will be <b>excluded</b> from your
                    weekly availability.</p>
                </div>
                <div class="mb-4 pb-2" id="excluded-dates">
                  <div class="card" id="excluded-dates-box">
                    <div class="p-3">
                      <button id="add-excluded-date-btn" class="btn btn-link p-0" type="button">+ Add new
                        date</button>
                    </div>
                  </div>
                </div>
                {{-- <div class="w-100 d-block pb-5">
                </div>
                <div class="pt-3 pb-3 bg-white sticky-top d-flex flex-wrap align-items-start">
                <button class="btn btn-outline-primary" type="button">View advanced booking type settings</button>
                <div class="col-12 col-md small text-secondary pt-4 pt-md-0 ps-md-5 mb-4">Define time windows
                  around
                  bookings, booking limits, location, Zoom/Google/Microsoft Teams links, paid bookings, group
                  bookings,
                  questions to invitees, custom email reminders, confirmation redirect URL and privacy.</div>
              </div> --}}
              </div>
            </div>
          </div>
          <div class="position-sticky bottom-0 row justify-content-center">
            <div class="col-md-12 col-lg-9">
              <div class="px-4 bg-white rounded shadow mt-2">
                <div class="flex flex-wrap py-2 py-sm-3 d-flex align-items-center justify-content-end border-top">
                  <a class="btn btn-outline-gray-800 my-1 my-sm-2 me-3"
                    href="{{ route('appointments_manage') }}">Cancel</a>
                  <button class="btn btn-primary my-1 my-sm-2" type="submit">
                    @if (isset($appointment))
                      {{ __('Edit booking type') }}
                    @else
                      {{ __('Create booking type') }}
                    @endif
                  </button>
                </div>
              </div>
            </div>
          </div>
        </fieldset>
      </form>
    </div>
  </div>
  {{-- <div class="position-sticky bottom-0">
  <h1>hiii</h1>
</div> --}}
@endsection

@section('scripts')
  @vite('public/js/appointment-dashboard.js')
  <script type="module">
    import {
      addAvailableTime,
      extractTimeFromTimestamp,
      createExcludeDate
    } from "{{ Vite::asset('public/js/appointment-dashboard.js') }}"

    @if (isset($available) && $available != null)

      let data = JSON.parse(`{!! json_encode($available) !!}`);
      $(".day-element").each(function() {
        let stamps = data.filter((stamp) => stamp.key == $(this).data("day"));
        if (stamps.length > 0) {
          stamps.forEach((stamp) => {
            addAvailableTime($(this), extractTimeFromTimestamp(stamp.from_date, stamp.to_date));
          })
        } else {
          addAvailableTime($(this));
        }
      });
    @else
      $(".day-element").each(function() {
        addAvailableTime($(this));
      });
    @endif

    @if (isset($excluded) && $excluded != null)
      let excluded = JSON.parse(`{!! json_encode($excluded) !!}`);
      if (excluded.length > 0) {
        excluded.forEach((date) => {
          createExcludeDate(date);
        })
      }
    @endif

    if ($("#timezone").attr("data-hasTimezone") != "true") {
      $("#timezone option").each(function() {
        if ($(this).attr("value") == Intl.DateTimeFormat().resolvedOptions().timeZone) {
          $(this).attr("selected", true);
        }
      })
    }
  </script>
@endsection
