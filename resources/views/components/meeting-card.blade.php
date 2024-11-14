@props([
    'id',
    'color' => null,
    'title' => 'unknown',
    'notes' => 'no notes',
    'admin' => null,
    'duration' => '15min',
    'date' => 'july 15, 2024',
    'href' => '#',
    'timezone' => 'Europe/Istanbul',
    'appointmentDate' => '2024-07-03 11:57:00',
    'googleCalendar' => null,
    'editable' => false,
    'disabled' => false,
    'selectable' => true,
    'style' => '',
    'butonClass' => '',
])

@aware(['appointmentDate', 'googleCalendar', 'butonClass'])

<?php
// start date & time relevant to the timezone of the appointment
$start = \Carbon\Carbon::parse($appointmentDate)->shiftTimezone($timezone ?? 'UTC');
// start & end date & time relevant to the timezone of the user (getting his timezone from his profile settings)
$relativeStart = $start->copy()->setTimezone(auth()->user()->timezone ?? 'UTC');

$startUTC = $start->copy()->setTimezone('UTC');
$endUTC = (clone $startUTC)->addMinutes(intval($duration));
?>

<div class="card appointment bordered-card mb-3 rounded" style="--br-clr: {{ $color }};{{ $style }}">
  <div class="card-body">
    <h5 class="card-title mb-3">
      @if (($editable && !$disabled) || ($selectable && !$disabled))
        <a href="{{ $href }}">{{ $title }}</a>
      @else
        {{ $title }}
      @endif
    </h5>
    <div class="d-flex align-items-center gap-4 mb-2 text-muted">
      <div class="d-flex align-items-center gap-2">
        <i style="height: 15px;" class="d-block">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-100 align-top" viewBox="0 0 384 512" fill="currentColor">
            <path
              d="M0 32C0 14.3 14.3 0 32 0H64 320h32c17.7 0 32 14.3 32 32s-14.3 32-32 32V75c0 42.4-16.9 83.1-46.9 113.1L237.3 256l67.9 67.9c30 30 46.9 70.7 46.9 113.1v11c17.7 0 32 14.3 32 32s-14.3 32-32 32H320 64 32c-17.7 0-32-14.3-32-32s14.3-32 32-32V437c0-42.4 16.9-83.1 46.9-113.1L146.7 256 78.9 188.1C48.9 158.1 32 117.4 32 75V64C14.3 64 0 49.7 0 32zM96 64V75c0 25.5 10.1 49.9 28.1 67.9L192 210.7l67.9-67.9c18-18 28.1-42.4 28.1-67.9V64H96zm0 384H288V437c0-25.5-10.1-49.9-28.1-67.9L192 301.3l-67.9 67.9c-18 18-28.1 42.4-28.1 67.9v11z" />
          </svg>
        </i>
        {{ $duration }}
      </div>
      @if ($admin != null)
        <div class="d-flex align-items-center gap-2">
          <i class="fa fa-user"></i> {{ $admin }}
        </div>
      @endif
    </div>
    @if (!$selectable)
      <hr>
      <div class="d-flex align-items-center gap-3 mb-2 text-muted my-2">
        <div class="d-flex align-items-center gap-2">
          <i class="fas fa-calendar-alt"></i>
          {{ $date }}
          {{ $relativeStart->format('h:i a') }}
        </div>
      </div>
      <hr>
    @endif
    <p class="card-text my-3">{{ $notes }}</p>
    @if ($editable && !$disabled)
      <hr>
      <div class="d-flex gap-3 py-3">
        @if ($googleCalendar != null)
          <a class="d-flex gap-2" target="_blank" href="{{ $googleCalendar }}">
            <svg version="1.1" id="Livello_1" xmlns="http://www.w3.org/2000/svg" width="20"
              xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 200 200"
              enable-background="new 0 0 200 200" xml:space="preserve">
              <g>
                <g transform="translate(3.75 3.75)">
                  <path fill="#FFFFFF"
                    d="M148.882,43.618l-47.368-5.263l-57.895,5.263L38.355,96.25l5.263,52.632l52.632,6.579l52.632-6.579 l5.263-53.947L148.882,43.618z" />
                  <path fill="#1A73E8" d="M65.211,125.276c-3.934-2.658-6.658-6.539-8.145-11.671l9.132-3.763c0.829,3.158,2.276,5.605,4.342,7.342
                      c2.053,1.737,4.553,2.592,7.474,2.592c2.987,0,5.553-0.908,7.697-2.724s3.224-4.132,3.224-6.934c0-2.868-1.132-5.211-3.395-7.026
                      s-5.105-2.724-8.5-2.724h-5.276v-9.039H76.5c2.921,0,5.382-0.789,7.382-2.368c2-1.579,3-3.737,3-6.487
                      c0-2.447-0.895-4.395-2.684-5.855s-4.053-2.197-6.803-2.197c-2.684,0-4.816,0.711-6.395,2.145s-2.724,3.197-3.447,5.276
                      l-9.039-3.763c1.197-3.395,3.395-6.395,6.618-8.987c3.224-2.592,7.342-3.895,12.342-3.895c3.697,0,7.026,0.711,9.974,2.145
                      c2.947,1.434,5.263,3.421,6.934,5.947c1.671,2.539,2.5,5.382,2.5,8.539c0,3.224-0.776,5.947-2.329,8.184
                      c-1.553,2.237-3.461,3.947-5.724,5.145v0.539c2.987,1.25,5.421,3.158,7.342,5.724c1.908,2.566,2.868,5.632,2.868,9.211
                      s-0.908,6.776-2.724,9.579c-1.816,2.803-4.329,5.013-7.513,6.618c-3.197,1.605-6.789,2.421-10.776,2.421
                      C73.408,129.263,69.145,127.934,65.211,125.276z" />
                  <path fill="#1A73E8"
                    d="M121.25,79.961l-9.974,7.25l-5.013-7.605l17.987-12.974h6.895v61.197h-9.895L121.25,79.961z" />
                  <path fill="#EA4335"
                    d="M148.882,196.25l47.368-47.368l-23.684-10.526l-23.684,10.526l-10.526,23.684L148.882,196.25z" />
                  <path fill="#34A853" d="M33.092,172.566l10.526,23.684h105.263v-47.368H43.618L33.092,172.566z" />
                  <path fill="#4285F4"
                    d="M12.039-3.75C3.316-3.75-3.75,3.316-3.75,12.039v136.842l23.684,10.526l23.684-10.526V43.618h105.263 l10.526-23.684L148.882-3.75H12.039z" />
                  <path fill="#188038"
                    d="M-3.75,148.882v31.579c0,8.724,7.066,15.789,15.789,15.789h31.579v-47.368H-3.75z" />
                  <path fill="#FBBC04" d="M148.882,43.618v105.263h47.368V43.618l-23.684-10.526L148.882,43.618z" />
                  <path fill="#1967D2"
                    d="M196.25,43.618V12.039c0-8.724-7.066-15.789-15.789-15.789h-31.579v47.368H196.25z" />
                </g>
              </g>
            </svg>
            See Event</a>
        @else
          <a class="d-flex gap-2" target="_blank"
            href="https://calendar.google.com/calendar/r/eventedit?text={{ $title }}&dates={{ $startUTC->format('Ymd\THis\Z') }}/{{ $endUTC->format('Ymd\THis\Z') }}&details={{ $notes . '<br><br>you can chekcout yur meetings at: ' . route('profile.meetings') }}&location={{ route('profile.meetings') }}">
            <svg version="1.1" id="Livello_1" xmlns="http://www.w3.org/2000/svg" width="20"
              xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 200 200"
              enable-background="new 0 0 200 200" xml:space="preserve">
              <g>
                <g transform="translate(3.75 3.75)">
                  <path fill="#FFFFFF"
                    d="M148.882,43.618l-47.368-5.263l-57.895,5.263L38.355,96.25l5.263,52.632l52.632,6.579l52.632-6.579 l5.263-53.947L148.882,43.618z" />
                  <path fill="#1A73E8" d="M65.211,125.276c-3.934-2.658-6.658-6.539-8.145-11.671l9.132-3.763c0.829,3.158,2.276,5.605,4.342,7.342
                      c2.053,1.737,4.553,2.592,7.474,2.592c2.987,0,5.553-0.908,7.697-2.724s3.224-4.132,3.224-6.934c0-2.868-1.132-5.211-3.395-7.026
                      s-5.105-2.724-8.5-2.724h-5.276v-9.039H76.5c2.921,0,5.382-0.789,7.382-2.368c2-1.579,3-3.737,3-6.487
                      c0-2.447-0.895-4.395-2.684-5.855s-4.053-2.197-6.803-2.197c-2.684,0-4.816,0.711-6.395,2.145s-2.724,3.197-3.447,5.276
                      l-9.039-3.763c1.197-3.395,3.395-6.395,6.618-8.987c3.224-2.592,7.342-3.895,12.342-3.895c3.697,0,7.026,0.711,9.974,2.145
                      c2.947,1.434,5.263,3.421,6.934,5.947c1.671,2.539,2.5,5.382,2.5,8.539c0,3.224-0.776,5.947-2.329,8.184
                      c-1.553,2.237-3.461,3.947-5.724,5.145v0.539c2.987,1.25,5.421,3.158,7.342,5.724c1.908,2.566,2.868,5.632,2.868,9.211
                      s-0.908,6.776-2.724,9.579c-1.816,2.803-4.329,5.013-7.513,6.618c-3.197,1.605-6.789,2.421-10.776,2.421
                      C73.408,129.263,69.145,127.934,65.211,125.276z" />
                  <path fill="#1A73E8"
                    d="M121.25,79.961l-9.974,7.25l-5.013-7.605l17.987-12.974h6.895v61.197h-9.895L121.25,79.961z" />
                  <path fill="#EA4335"
                    d="M148.882,196.25l47.368-47.368l-23.684-10.526l-23.684,10.526l-10.526,23.684L148.882,196.25z" />
                  <path fill="#34A853" d="M33.092,172.566l10.526,23.684h105.263v-47.368H43.618L33.092,172.566z" />
                  <path fill="#4285F4"
                    d="M12.039-3.75C3.316-3.75-3.75,3.316-3.75,12.039v136.842l23.684,10.526l23.684-10.526V43.618h105.263 l10.526-23.684L148.882-3.75H12.039z" />
                  <path fill="#188038"
                    d="M-3.75,148.882v31.579c0,8.724,7.066,15.789,15.789,15.789h31.579v-47.368H-3.75z" />
                  <path fill="#FBBC04" d="M148.882,43.618v105.263h47.368V43.618l-23.684-10.526L148.882,43.618z" />
                  <path fill="#1967D2"
                    d="M196.25,43.618V12.039c0-8.724-7.066-15.789-15.789-15.789h-31.579v47.368H196.25z" />
                </g>
              </g>
            </svg> Add To Calendar</a>
        @endif
      </div>
    @endif
    @if ($editable && !$disabled)
      <hr>
      <div class="d-flex justify-content-between align-items-center mt-3">
        <button class="btn mb-2 btn-outline-primary {{ $butonClass }}" data-id="{{ $id }}">
          Add Note <i class="fa fa-edit"></i>
        </button>
      </div>
    @endif
    @if ($selectable && !$disabled)
      <hr>
      <div class="d-flex justify-content-between align-items-center mt-3 w-100">
        <a class="btn btn-outline-primary w-100 border bordered {{ $butonClass }}" href="{{ $href }}">
          Book Now
        </a>
      </div>
    @endif
  </div>
</div>
