@extends('dashboard.layout')

@section('meta')
  @php $page_title = config("app.name") . " | " . __('appointments') @endphp
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('styles')
  @vite(['resources/css/dashboard/style.css', 'resources/css/dashboard/appointment-dashboard.css'])
@endsection

@section('content')
  <div class="container">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
      <div class="d-block mb-4 mb-md-0">
        <x-breadcrumb page="appointments" is-route=1 url="appointments_manage" />
        <h2 class="h4 text-capitalize">{{ __('my appointments') }}</h2>
        <p class="mb-0">{{ __('manage :all from one place.', ['all' => __('your appointments')]) }}</p>
      </div>
      <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('appointment_create') }}"
          class="btn btn-sm btn-gray-800 d-inline-flex align-items-center text-capitalize">
          <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
            </path>
          </svg>
          {{ __('New :item', ['item' => __('appointment')]) }}
        </a>
      </div>
    </div>
  </div>
  <div class="container mt-5">
    <!-- Tab -->
    <nav>
      <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="nav-types-tab" data-bs-toggle="tab" href="#nav-types" data-url="nav-types"
          role="tab" aria-controls="nav-types" aria-selected="true">Appointment Types</a>
        <a class="nav-item nav-link" id="nav-scheduled-tab" data-bs-toggle="tab" href="#nav-scheduled"
          data-url="nav-scheduled" role="tab" aria-controls="nav-scheduled" aria-selected="false">Scheduled
          Appointments</a>
      </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
      <div class="tab-pane fade show active" id="nav-types" role="tabpanel" aria-labelledby="nav-types-tab">
        <div class="py-4 pt-5 bg-white">
          <div class="container">
            @if ($appointments->count() > 0)
              <div class="row">
                @foreach ($appointments as $appointment)
                  <div class="col-md-4">
                    <div class="card appointment bordered-card mb-3" style="--br-clr: {{ $appointment->color }}">
                      <div class="card-body">
                        <h5 class="card-title"><a
                            href="{{ route('appointment_edit', $appointment->id) }}">{{ $appointment->title }}</a>
                        </h5>
                        <p class="card-text">{{ $appointment->description }}</p>
                        <a href="{{ route('appointment_booking', $appointment->url) }}" class="btn btn-link">View booking
                          page</a>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                          <a class="btn btn-outline-primary" href="{{ route('appointment_edit', $appointment->id) }}">
                            Edit <i class="fa fa-edit"></i>
                          </a>
                          <button data-id="{{ $appointment->id }}"
                            class="on-off-btn btn btn-outline-gray-800 text-capitalize">
                            @if ($appointment->status == 1)
                              Turn Off
                            @else
                              Turn On
                            @endif
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
                <p style="margin: 0px;">{{ __('there is no :items to show', ['items' => __('appointments')]) }}.</p>
              </div>
            @endif
          </div>
        </div>
      </div>
      <div class="tab-pane fade" id="nav-scheduled" role="tabpanel" aria-labelledby="nav-scheduled-tab">
        <div class="py-4 pt-5 bg-white">
          <div class="container">
            <div class="row justify-content-center">

              <div class="col-12 col-lg-11">

                <div class="d-flex flex-wrap align-items-center">
                  <h1 class="h3 fw-bold d-inline me-3 mb-3 mb-md-0">
                    Bookings <small class="text-secondary">{{ $booked_appointments->count() }}</small>
                  </h1>

                  <div class="col-12 col-md">
                    <div class="d-flex align-items-center justify-content-start justify-content-lg-end flex-wrap">
                      <div class="d-flex align-items-center flex-wrap me-3 mb-3 mb-md-0">
                        <div class="me-2">Filter:</div>
                        <form method="get">
                          <select name="show" class="form-control" oninput="this.form.submit()">
                            <option @if (request('show') == null || !in_array(request('show'), ['past', 'cancelled', 'all'])) selected @endif value="upcoming">Upcoming bookings
                            </option>
                            <option @if (request('show') == 'past') selected @endif value="past">Past bookings
                            </option>
                            <option @if (request('show') == 'cancelled') selected @endif value="cancelled">Cancelled bookings
                            </option>
                            <option @if (request('show') == 'all') selected @endif value="all">All bookings</option>
                          </select>
                        </form>
                      </div>
                      <a href="{{ route('booked_appointment_export') }}"
                        class="btn btn btn-outline-gray-900 me-3 mb-3 mb-md-0">
                        Export bookings
                        <div class="icon-info ms-1" data-bs-toggle="tooltip" data-bs-placement="top"
                          data-bs-title="Click here to download your bookings as an .CSV file">?</div>
                      </a>

                      <div>
                        <a href="/calendar" class="btn btn-outline-gray-900 mb-3 mb-md-0 px-2" data-bs-toggle="tooltip"
                          data-bs-placement="top" data-bs-title="Switch to Calendar View"
                          data-bs-original-title="Calendar view">
                          <svg width="24" height="24" data-name="List Icon" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24">
                            <defs>
                              <style>
                                .calendarIcon {
                                  fill: none;
                                  stroke: currentColor;
                                  stroke-linecap: round;
                                  stroke-linejoin: round;
                                  stroke-width: 1.2px;
                                }
                              </style>
                            </defs>
                            <rect class="calendarIcon" x="2" y="4" width="20" height="18" rx="2"
                              ry="2"></rect>
                            <line class="calendarIcon" x1="8" y1="2" x2="8" y2="6">
                            </line>
                            <line class="calendarIcon" x1="16" y1="2" x2="16" y2="6">
                            </line>
                            <line class="calendarIcon" x1="2" y1="10" x2="22" y2="10">
                            </line>
                            <line class="calendarIcon" x1="2" y1="16" x2="22" y2="16">
                            </line>
                            <line class="calendarIcon" x1="9" y1="10" x2="9" y2="22">
                            </line>
                            <line class="calendarIcon" x1="15" y1="10" x2="15" y2="22">
                            </line>
                          </svg>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="w-100 d-block mb-2 mb-sm-4 pb-4"></div>
                <div class="text-end mb-n1 d-lg-none">
                  <div class="small d-xxl-none text-muted lh-1 opacity-3"><small>Scroll →</small></div>
                </div>

                <div class="table-responsive mb-4">
                  <table class="table align-middle">
                    <thead>
                      <tr>
                        <th class="small fw-normal text-secondary ps-0">Date</th>
                        <th class="small fw-normal text-secondary">Time</th>
                        <th class="small fw-normal text-secondary">Duration</th>
                        <th class="small fw-normal text-secondary">Booking Type</th>
                        <th class="small fw-normal text-secondary">With</th>
                        <th class="small fw-normal text-secondary">Status</th>
                        <th class="small fw-normal text-secondary">Actions</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody class="border-top">

                      @foreach ($booked_appointments as $booked)
                        <tr class="">
                          <td class="py-4 text-nowra pe-4 ps-0 fw-bold text-nowrap" style="min-width: 140px">
                            {{-- June 17, 2024 --}}
                            {{ $booked->day() }}
                          </td>
                          <td class="py-4 small text-nowrap pe-4">
                            {{-- 8:30 AM --}}
                            {{ $booked->time() }}
                          </td>
                          <td class="py-4 small text-nowrap pe-4">
                            {{-- 15 m --}}
                            {{ $booked->appointment->show_duration() }}
                          </td>
                          <td class="py-4 small" style="min-width: 200px">

                            <a class="text-decoration-none"
                              href="{{ route('appointment_edit', $booked->appointment->id) }}">
                              {{-- 15 Minute appointment --}}
                              {{ $booked->appointment->title }}
                            </a>
                          </td>
                          <td class="py-4 small" style="min-width: 200px">
                            {{-- ttt --}}
                            {{ $booked->booker->fullname() }}
                          </td>
                          <td class="py-4 text-center">
                            <p class="d-flex justify-content-center align-items-center m-0">
                              <span
                                class="badge py-2 px-3 rounded-pill {{ ['bg-danger', 'bg-success', 'bg-primary'][$booked->status] }}">
                                {{ ['Cancelled', 'Scheduled', 'Past'][$booked->status] }}
                              </span>
                            </p>
                          </td>
                          <td class="py-4">
                            <div class="d-flex align-items-center justify-content-start">
                              @if ($booked->status == 1)
                                <button class="cancel-btn btn btn-sm btn-outline-danger border-0 m-1"
                                  data-id="{{ $booked->id }}">
                                  Cancel
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-tertiary border-0 m-1"
                                  data-bs-toggle="modal" data-bs-target="#modal-form-{{ $booked->id }}">
                                  Reschedule
                                </button>
                                <div class="modal fade" id="modal-form-{{ $booked->id }}" tabindex="-1"
                                  aria-labelledby="modal-form-{{ $booked->id }}" style="display: none;"
                                  aria-hidden="true">
                                  <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                      <div class="modal-body p-0">
                                        <div class="card p-3 p-lg-4"><button type="button" class="btn-close ms-auto"
                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                          <div class="text-center text-md-center mb-4 mt-md-0">
                                            <h1 class="mb-0 h4">Reschedule Appointment</h1>
                                          </div>
                                          <div class="mb-3 w-100">
                                            <label for="date">Date</label>
                                            <div class="input-group">
                                              <span class="input-group-text">
                                                <svg class="icon icon-xs text-gray-600" fill="currentColor"
                                                  viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                  <path fill-rule="evenodd"
                                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                    clip-rule="evenodd"></path>
                                                </svg>
                                              </span>
                                              <input data-datepicker="" class="form-control datepicker-input"
                                                id="date" type="text"
                                                value="{{ $booked->appointment_date->format('m/d/Y') }}"
                                                placeholder="dd/mm/yyyy" required>
                                            </div>
                                          </div>
                                          <div class="mb-3 w-100">
                                            <label for="date">Time</label>
                                            <div class="input-group" style="min-width: 160px;">
                                              <select class="form-control" name="time_hour">
                                                @for ($i = 0; $i <= 12; $i++)
                                                  <option @if ($i == $booked->hour()) selected @endif
                                                    value="{{ $i <= 9 ? '0' . $i : $i }}">
                                                    {{ $i <= 9 ? '0' . $i : $i }}</option>
                                                @endfor
                                              </select>
                                              <span class="input-group-text bg-white" style="padding: 0px 3px;">:</span>
                                              <select class="form-control" name="time_minut">
                                                @for ($i = 0; $i <= 59; $i++)
                                                  <option @if ($i == $booked->minute()) selected @endif
                                                    value="{{ $i <= 9 ? '0' . $i : $i }}">
                                                    {{ $i <= 9 ? '0' . $i : $i }}</option>
                                                @endfor
                                              </select>
                                              <select class="form-control" name="time_format">
                                                <option {{ $booked->format() == 'AM' ? 'selected' : '' }}
                                                  value="am">
                                                  AM</option>
                                                <option {{ $booked->format() == 'PM' ? 'selected' : '' }}
                                                  value="pm">
                                                  PM</option>
                                              </select>
                                            </div>
                                          </div>
                                          <div class="pb-4 w-100 d-block"></div>
                                          <div
                                            class="d-flex justify-content-between align-items-center sticky-bottom bg-white py-3 py-sm-4 border-top">
                                            <div></div>
                                            <button class="reschedule-btn btn btn-gray-800" data-bs-dismiss="modal"
                                              aria-label="Close">Save</button>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              @endif
                              <button type="button" data-bs-toggle="modal"
                                data-bs-target="#modal-{{ $booked->id }}"
                                class="btn btn-sm btn-outline-primary text-nowrap border-0 my-1 ms-1">Details</button>
                            </div>
                          </td>
                        </tr>
                      @endforeach

                    </tbody>
                  </table>
                </div>

                @if ($booked_appointments->count() > 0)
                  @foreach ($booked_appointments as $booked)
                    <div class="modal fade" id="modal-{{ $booked->id }}" tabindex="-1"
                      aria-labelledby="BookedAppointmentModal" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-body p-0">
                            <div class="card p-3 p-lg-4">
                              <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                              <div class="text-center text-md-center pb-4 mt-md-0 border-bottom">
                                <h1 class="mb-0 h4">Appointment Details</h1>
                              </div>
                              <div>
                                <h2 class="h5 mb-4 pt-2 fw-bold">{{ $booked->title }}</h2>
                              </div>
                              <div class="d-flex align-items-center mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                  class="d-inline-block me-2" viewBox="0 0 14 14">
                                  <g id="Group_37" data-name="Group 37" transform="translate(-628.5 -309.671)">
                                    <g id="Group_18" data-name="Group 18" transform="translate(629 310.235)">
                                      <rect id="Rectangle_14" data-name="Rectangle 14" width="13" height="12"
                                        rx="2" transform="translate(0 0.936)" stroke-width="1" stroke="#000"
                                        stroke-linecap="round" stroke-linejoin="round" fill="none" />
                                      <line id="Line_3" data-name="Line 3" y2="2"
                                        transform="translate(4 -0.064)" fill="none" stroke="#000"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="1" />
                                      <line id="Line_4" data-name="Line 4" y2="2"
                                        transform="translate(9 -0.064)" fill="none" stroke="#000"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="1" />
                                      <line id="Line_5" data-name="Line 5" x2="13"
                                        transform="translate(0 4.936)" fill="none" stroke="#000"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="1" />
                                    </g>
                                  </g>
                                </svg>
                                <div>
                                  <small>{{ $booked->day() . ' ' . $booked->time() }}</small>
                                </div>
                              </div>
                              <div class="d-flex align-items-center mb-2">
                                <svg class="d-inline-block me-2" xmlns="http://www.w3.org/2000/svg" width="15"
                                  height="15" viewBox="0 0 15 15">
                                  <g id="Group_38" data-name="Group 38" transform="translate(-298.5 -275.5)">
                                    <g id="Group_7" data-name="Group 7" transform="translate(299 276)">
                                      <circle id="Ellipse_2" data-name="Ellipse 2" cx="7" cy="7" r="7"
                                        stroke-width="1" stroke="#000" stroke-linecap="round" stroke-linejoin="round"
                                        fill="none" />
                                      <path id="Path_3" data-name="Path 3" d="M-68,407v3.5l3.5,1.4"
                                        transform="translate(75 -403.5)" fill="none" stroke="#000"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="1" />
                                    </g>
                                  </g>
                                </svg>

                                <div>
                                  <small>{{ $booked->appointment->show_duration() }}</small>
                                </div>
                              </div>
                              <div class="d-block w-100 pt-3 pb-1"></div>
                              <div class="mb-2">
                                <small>
                                  <strong>With:</strong>
                                  {{ $booked->booker->fullname() }}
                                </small>
                              </div>
                              <div class="mb-2">
                                <small><strong>Email: </strong><a class="text-decoration-none"
                                    href="mailto:{{ $booked->booker->email }}">{{ $booked->booker->email }}</a></small>
                              </div>
                              <div class="mb-2">
                                <small><strong>At: </strong>{{ $booked->time() }}</small>
                              </div>
                              <div class="mb-2">
                                <small><strong>Attendee Timezone: </strong>Europe/Istanbul</small>
                              </div>
                              <div class="mb-2">
                                <small><strong>Created: </strong> {{ $booked->created_at_date() }}</small>
                              </div>
                              <div class="pb-4 w-100 d-block"></div>
                              <div
                                class="d-flex justify-content-between align-items-center sticky-bottom bg-white py-3 py-sm-4 border-top">
                                <div></div>
                                <button class="btn btn-primary" data-bs-dismiss="modal"
                                  aria-label="Close">Close</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                @else
                  <div class="alert alert-danger">No Booked Appointments To Show</div>
                @endif

              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
    <!-- End of tab -->
  </div>
  <div class="container">
    <div class="position-fixed" id="alerts-holder" style="bottom:15px">
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    const hash = window.location.hash;
    if (hash) {
      changePage(hash.substring(1));
    } else {
      changePage("nav-types")
    }
    window.addEventListener("popstate", (e) => {
      let state = e.state;
      let pageId = state?.pageId;
      if (pageId) {
        changePage(pageId);
      } else {
        changePage("nav-types")
      }
    });

    let btn = $('.on-off-btn');

    btn.each(function() {
      $(this).on("click", function() {
        $(this).toggleClass(['btn-outline-gray-800', 'btn-outline-gray-500'])
        if ($(this).hasClass("btn-outline-gray-800")) {
          fetch(`{{ route('appointments_manage') }}/${$(this).data("id")}/status`, {
              method: "POST",
              // body: new FormData(),
              headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]')
                  .attr("content"),
              },
              body: JSON.stringify({
                status: "on",
              }),
            })
            .then((response) => response.json())
            .then((data) => {
              console.log(data);
              $(this).text('{{ __('turn off') }}')
            })
            .catch((error) => console.error("Error:", error));
        } else {
          fetch(`{{ route('appointments_manage') }}/${$(this).data("id")}/status`, {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
              },
              body: JSON.stringify({
                status: "off",
              }),
            })
            .then((response) => response.json())
            .then((data) => {
              console.log(data);
              $(this).text('{{ __('turn on') }}')
            })
            .catch((error) => console.error("Error:", error));

        }
      });
    });

    $("#nav-types-tab, #nav-scheduled-tab").each(function() {
      $(this).on("click", function() {
        processAjaxData($(this).data("url"));
      })
    });

    function changePage(pageId) {
      $(`#${pageId}-tab`).addClass("active").siblings().removeClass("active")
      $(`#${$(`#${pageId}-tab`).data("url")}`).addClass("active show").siblings().removeClass("active show")
    }

    function processAjaxData(pageId) {
      let location = window.location;
      let origin = location.origin;
      let path = location.pathname;
      let urlPath = `${origin}${path}#${pageId}`;
      history.pushState({
        pageId: pageId
      }, "", urlPath);
    }

    function makeAlert(text) {
      let id = Date.now();
      $("#alerts-holder").append(`
        <div class="toast bg-primary mb-3 show" role="alert" aria-live="assertive" aria-atomic="true" id="${id}">
          <div class="toast-header">
            <svg class="icon icon-xs text-gray-500 me-2" width="13" height="15" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
            <strong class="me-auto">notification</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
          <div class="toast-body text-white">
            ${text}
          </div>
        </div>
      `)
      new bootstrap.Alert(document.getElementById(id))
      return document.getElementById(id);
    }

    $('.cancel-btn').each(function() {
      $(this).on('click', function() {
        Swal.fire({
          title: "Are You Sure",
          text: "You Want To Cancel The Appointment ?",
          customClass: {
            confirmButton: "btn btn-danger",
            cancelButton: "btn btn-primary me-4",
          },
          reverseButtons: true,
          buttonsStyling: false,
          showCancelButton: true,
          confirmButtonText: "Delete",
        }).then((result) => {
          if (result.isConfirmed) {
            fetch(`{{ url('en/dashboard/bookedAppointments') }}/${$(this).data("id")}`, {
                method: "POST",
                headers: {
                  "Content-Type": "application/json",
                  "X-CSRF-TOKEN": $('meta[name="csrf-token"]')
                    .attr("content"),
                },
                body: JSON.stringify({
                  _method: "DELETE",
                  appointment_id: $(this).data("id"),
                }),
              })
              .then((response) => response.json())
              .then((data) => console.log(data))
              .catch((error) => console.error("Error:", error));
            const notyf = new Notyf({
              duration: 3000,
              position: {
                x: 'right',
                y: 'top',
              },
              types: [{
                type: 'error',
                background: '#FA5252',
                className: 'rounded',
                icon: {
                  className: 'notyf__icon--error',
                  tagName: 'span',
                  color: 'rgb(237, 61, 61)'
                },
                dismissible: false
              }]
            });
            notyf.open({
              type: 'error',
              message: 'appointment is cancelled !'
            });
          }
        });

      });
    })

    /*
        function getTimeZones() {
          const timeZones = Intl.supportedValuesOf('timeZone');

          return timeZones.map(timeZone => {
            const now = new Date();
            const formatter = new Intl.DateTimeFormat('en-US', {
              timeZone,
              hour: 'numeric',
              minute: 'numeric',
              second: 'numeric',
              timeZoneName: 'short'
            });

            const parts = formatter.formatToParts(now);
            const offset = parts.find(part => part.type === 'timeZoneName').value;

            return {
              timeZone,
              offset
            };
          });
        }
        const timeZoneList = getTimeZones();
        timeZoneList.forEach(zone => {
          console.log(`${zone.offset} ${zone.timeZone}`);
        });

        function convertTimeZone(date, fromTimeZone, toTimeZone) {
          // Create a formatter for the source time zone
          const fromFormatter = new Intl.DateTimeFormat('en-US', {
            timeZone: fromTimeZone,
            year: 'numeric',
            month: 'numeric',
            day: 'numeric',
            hour: 'numeric',
            minute: 'numeric',
            second: 'numeric'
          });

          // Format the date in the source time zone
          const fromParts = fromFormatter.formatToParts(date);
          const fromDate = new Date(
            `${fromParts.find(part => part.type === 'year').value}-${fromParts.find(part => part.type === 'month').value}-${fromParts.find(part => part.type === 'day').value}T${fromParts.find(part => part.type === 'hour').value}:${fromParts.find(part => part.type === 'minute').value}:${fromParts.find(part => part.type === 'second').value}`
          );

          // Create a formatter for the target time zone
          const toFormatter = new Intl.DateTimeFormat('en-US', {
            timeZone: toTimeZone,
            year: 'numeric',
            month: 'numeric',
            day: 'numeric',
            hour: 'numeric',
            minute: 'numeric',
            second: 'numeric',
            timeZoneName: 'short'
          });

          // Format the date in the target time zone
          return toFormatter.format(fromDate);
        }

        // Example usage
        const dateInNewYork = new Date('2024-06-21T15:00:00'); // 3:00 PM on June 21, 2024
        const convertedDate = convertTimeZone(dateInNewYork, 'America/New_York', 'Europe/Istanbul');
        console.log(`Date in ${toTimeZone}: ${convertedDate}`);
     */
  </script>
@endsection

@section('jslibs')
  <script src="{{ url('libs/dashboard/datepicker.min.js') }}"></script>
  <script src="{{ url('libs/dashboard/sweetalert2.all.min.js') }}"></script>
  <script src="{{ url('libs/dashboard/notyf.min.js') }}"></script>
@endsection
