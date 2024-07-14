<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\BookedAppointment;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Http\Requests\StoreBookedAppointmentRequest;
use App\Http\Requests\UpdateBookedAppointmentRequest;

use Illuminate\Validation\ValidationException;

use App\Http\Controllers\GoogleCalendarController;
use App\Models\AppointmentSetting;
use Carbon\CarbonTimeZone;

class BookedAppointmentController extends Controller {

  public function __construct() {
    return $this->middleware(['auth', 'admin'])->only(["destroy"]);
  }

  public function export(Request $request) {

    $res = BookedAppointment::join("appointments", "appointments.id", "=", "booked_appointments.appointment_id")
      ->where("appointments.user_id", auth()->user()->id)
      ->with("booker")
      ->get();

    $fileName = now() . '_meetings.csv';
    $headers = array(
      "Content-type"        => "text/csv;charset=UTF-8",
      "Content-Disposition" => "attachment; filename=$fileName",
      "Pragma"              => "no-cache",
      "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
      "Expires"             => "0"
    );

    $row = ["id", "meeting_date", "meeting_status", "username", "full name", "user_email", "last_update", "created_at"];

    $callback = function () use ($res, $row) {
      $file = fopen('php://output', 'w');
      fputcsv($file, $row);
      foreach ($res as $data_row) {
        fputcsv($file, [
          $data_row->id,
          $data_row->appointment_date,
          $data_row->status == 1 ? "active" : ($data_row->status == 2 ? "pending" : "cancelled"),
          $data_row->booker->username,
          $data_row->booker->fullname(),
          $data_row->booker->email,
          $data_row->updated_at,
          $data_row->created_at,
        ]);
      }
      fclose($file);
    };

    return response()->stream($callback, 200, $headers);
  }

  public function list_booked(Request $request, Appointment $appointment) {

    $validated = $request->validate([
      "date" => "required|date|after:today",
    ]);

    $bookedDate = Carbon::parse($validated["date"]);
    $bookedDate->timezone(new CarbonTimeZone($appointment->timezone));

    $endDate = $bookedDate->copy()->addDay();
    $bookedAlready = $appointment
      ->booked()
      ->where("status", 1)
      ->whereBetween('appointment_date', [$bookedDate, $endDate])->get();

    $excluded = $appointment
      ->settings()
      ->where("key", "excluded")->get();

    $excluded = $excluded->filter(function (AppointmentSetting $setting, int $key) use ($bookedDate, $endDate) {
      $date = Carbon::createFromFormat('Y-m-d H:i:sP', $setting->getRawOriginal('day') . ' ' . $setting->from_date);
      return $date->between($bookedDate, $endDate, true);
    });

    return ["booked" => $bookedAlready->pluck("appointment_date"), "excluded" => $excluded->map(function (AppointmentSetting $setting) {
      return [
        "from" => Carbon::createFromFormat('Y-m-d H:i:sP', $setting->getRawOriginal('day') . ' ' . $setting->from_date),
        "to" => Carbon::createFromFormat('Y-m-d H:i:sP', $setting->getRawOriginal('day') . ' ' . $setting->to_date),
      ];
    })];
  }

  public function store(StoreBookedAppointmentRequest $request, Appointment $appointment) {
    // $date => Booked ISO Date And Time
    $date = $request->validated('date'); // Example: 2024-07-03T01:00:00
    $intent_id = $request->validated('intentId') ?? null; // Example: pi_3PWeO6IfgNuRq2TZ1dJcWLQs_secret_wYTC9D3ATGwd633dU9WazmXk2
    // $timezone = $request->validated("timezone");

    $days = [
      "Sunday",
      "Monday",
      "Tuesday",
      "Wednesday",
      "Thursday",
      "Friday",
      "Saturday",
    ];

    $settings = $appointment->settings;
    $date = Carbon::parse($date);
    // set the appointment timezone to compare it to the available time (which was set to same timezone)
    $day = $date->toDateString(); // Formats to 'Y-m-d'

    // Assuming $appointments is a collection of appointments
    $date->timezone(new \DateTimeZone($appointment->timezone));

    // get day key
    $available_days = $settings->where("key", array_search($date->format("l"), $days));

    if (!empty($available_days->count() > 0)) {

      // ===== check the Time Of the appointment ===== //
      foreach ($available_days as $timestamp) {

        $startTime = Carbon::createFromFormat('H:i:sP', substr($timestamp->from_date, 2), new \DateTimeZone($appointment->timezone));
        $startTime->setDate($date->year, $date->month, $date->day);

        $endTime = Carbon::createFromFormat('H:i:sP', substr($timestamp->to_date, 2), new \DateTimeZone($appointment->timezone));
        $endTime->setDate($date->year, $date->month, $date->day);

        if ($date->between($startTime, $endTime, true)) {

          // $checkTime =  Carbon::createFromFormat('h:i:A', $time, new \DateTimeZone($appointment->timezone));
          $excluded_days = $settings->where("key", "excluded");

          // Check if the time is within the specifically excluded time windows
          if (!empty($excluded_days)) {
            $excluded_days = $excluded_days->filter(function ($execluded) use ($day) {
              return Carbon::parse($execluded->day)->toDateString() === $day;
            });

            foreach ($excluded_days as $window) {
              $windowStart = Carbon::createFromFormat('Y-m-d H:i:sP', $window->day . ' ' . $window->from_date, new \DateTimeZone($appointment->timezone));
              $windowEnd = Carbon::createFromFormat('Y-m-d H:i:sP', $window->day . ' ' . $window->to_date, new \DateTimeZone($appointment->timezone));

              if ($date->between($windowStart, $windowEnd, true)) {
                // throw ValidationException::withMessages([
                //   'selected_date' => 'The selected time is within a specifically excluded time window.'
                // ]);
                return response()->json(['success' => "error", "message" => "The selected time is within a specifically excluded time window."]);
              }
            }
          }

          // Calculate the number of minutes since the start time
          $minutesSinceStart = $startTime->diffInMinutes($date);

          // Calculate the position within the time block (time block = duration of the meeting + the buffer zone after it)
          $positionInBlock = $minutesSinceStart % ($appointment->duration + $appointment->buffer_zone);

          // Check if the time is within the first 30 minutes of the 45-minute block
          if ($positionInBlock <= $appointment->duration) {

            $bookedDate = $date->copy();
            $conflicted_booked_appointments = BookedAppointment::where("appointment_id", $appointment->id)
              ->where("status", 1)
              ->whereBetween('appointment_date', [$date, $bookedDate->addMinutes($appointment->duration)])->get();

            if ($conflicted_booked_appointments->count() == 0) {

              if ($appointment->link_google_calendar == 1) {

                $googleCalendarController = new GoogleCalendarController();
                $eventLink = $googleCalendarController->createGoogleCalendarEvent(
                  $date->toAtomString(),
                  $date->copy()->addMinutes($appointment->duration)->toAtomString(),
                  'Appointment with ' . $appointment->author->fullname(),
                  'Details of the appointment.',
                  'Online'
                );

                if (isset($eventLink) && $eventLink instanceof \Illuminate\Http\RedirectResponse) {
                  return $eventLink; // Redirect to Google auth if needed
                }

                if ($eventLink) {
                  BookedAppointment::create([
                    "booker_id" => auth()->user()->id,
                    "appointment_id" => $appointment->id,
                    "secret_key" => $intent_id ?? "",
                    "appointment_date" => $date,
                    "meeting_link" => $eventLink,
                    "status" => $appointment->price != null && $appointment->price > 0 ? 2 : 1,
                  ]);
                } else {
                  dd('Failed to create Google Calendar event');
                }
              } else {
                BookedAppointment::create([
                  "booker_id" => auth()->user()->id,
                  "appointment_id" => $appointment->id,
                  "secret_key" => $intent_id ?? "",
                  "appointment_date" => $date,
                  "status" => $appointment->price != null && $appointment->price > 0 ? 2 : 1,
                ]);
              }

              if ($appointment->price != null && $appointment->price > 0) {
                return response()->json(['status' => "success", "message" => "appointment has been booked successfully."]);
              } else {
                return redirect()->route("profile.meetings");
              }
            } else {
              if ($appointment->price != null && $appointment->price > 0) {
                return response()->json(['status' => "error", "message" => "This time slot has been already booked."]);
              } else {
                throw ValidationException::withMessages([
                  'selected_date' => 'This time slot has been already booked.'
                ]);
              }
            }
          } else {
            if ($appointment->price != null && $appointment->price > 0) {
              return response()->json(['status' => "error", "message" => "The selected time is within the excluded itme block (the buffer zone between every appointment)."]);
            } else {
              throw ValidationException::withMessages([
                'selected_date' => 'The selected time is within the excluded itme block (the buffer zone between every appointment).'
              ]);
            }
          }
        } else {
          if ($appointment->price != null && $appointment->price > 0) {
            return response()->json(['status' => "error", "message" => "The selected time is outside the available range."]);
          } else {
            throw ValidationException::withMessages([
              'selected_date' => 'The selected time is outside the available range.'
            ]);
          }
        }
      }
    }

    if ($appointment->price != null && $appointment->price > 0) {
      return response()->json(['status' => "no status"]);
    } else {
      return throw ValidationException::withMessages([
        'selected_date' => 'something went wrong !'
      ]);
    }
  }

  public function update(UpdateBookedAppointmentRequest $request, BookedAppointment $bookedAppointment) {
    if (auth()->user()->id == $bookedAppointment->booker_id) {
      $bookedAppointment->notes = $request->validated("notes");
      $bookedAppointment->save();
    } else {
      abort(404);
    }
    return redirect()->route("profile.meetings");
  }

  public function destroy(BookedAppointment $bookedAppointment) {

    if ($bookedAppointment->appointment->user_id == auth()->user()->id) {
      $bookedAppointment->status = 0;
      $bookedAppointment->save();
    } else {
      abort(404);
    }
  }
  /*
  private function createGoogleCalendarEvent($date, $duration, $timezone, $appointment) {
    $client = $this->getClient();
    $service = new Google_Service_Calendar($client);

    $start = Carbon::parse($date, $timezone);
    $end = $start->copy()->addMinutes($duration);

    $event = new Google_Service_Calendar_Event([
      'summary' => $appointment->title,
      'description' => $appointment->notes ?? '',
      'start' => new Google_Service_Calendar_EventDateTime([
        'dateTime' => $start->toRfc3339String(),
        'timeZone' => $timezone,
      ]),
      'end' => new Google_Service_Calendar_EventDateTime([
        'dateTime' => $end->toRfc3339String(),
        'timeZone' => $timezone,
      ]),
      'conferenceData' => [
        'createRequest' => [
          'requestId' => 'sample123',
          'conferenceSolutionKey' => [
            'type' => 'hangoutsMeet'
          ],
        ],
      ],
      'location' => 'online' ?? '',
    ]);

    $event = $service->events->insert('primary', $event, ['conferenceDataVersion' => 1]);

    return $event->getHangoutLink();
  } */

  /* private function getClient() {
    $client = new Google_Client();
    $client->setApplicationName(env('APP_NAME'));
    $client->setScopes(Google_Service_Calendar::CALENDAR);
    $client->setAuthConfig(storage_path('app/google-calendar/calendar-credentials.json'));
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');
    $client->setRedirectUri(env('GOOGLE_CALENDAR_REDIRECT_URI'));

    // Load previously authorized token from a file, if it exists.
    if (Storage::exists('google-calendar-token.json')) {
      $accessToken = json_decode(Storage::get('google-calendar-token.json'), true);
      $client->setAccessToken($accessToken);

      if ($client->isAccessTokenExpired()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        Storage::put('google-calendar-token.json', json_encode($client->getAccessToken()));
      }
    } else {
      // Generate the authentication URL
      $authUrl = $client->createAuthUrl();

      return redirect($authUrl);
      // // Display the URL to the user
      // echo "Open the following link in your browser:\n$authUrl\n";
      // // Get the auth code from the user
      // echo 'Enter verification code: ';
      // $authCode = trim(fgets(\STDIN));

      // Exchange authorization code for an access token
      $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
      $client->setAccessToken($accessToken);

      // Store the credentials to disk.
      Storage::put('google-calendar-token.json', json_encode($client->getAccessToken()));
    }

    return $client;
  } */
}
