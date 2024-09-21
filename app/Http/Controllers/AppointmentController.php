<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Requests\AppointmentRequest;
use App\Models\BookedAppointment;
use App\Models\AppointmentSetting;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;

class AppointmentController extends Controller {

  public function index() {
    $appointments = Appointment::orderBy("created_at", "DESC")->paginate(config('settings.tables_row_count'));
    if (request("show") != null && in_array(request("show"), ["cancelled", "past", "all"])) {
      if (request("show") == "cancelled") {
        $booked_appointments = BookedAppointment::for(auth()->user()->id)->cancelled()->with(["appointment", "booker"])->paginate(config('settings.tables_row_count'));
      } elseif (request("show") == "past") {
        $booked_appointments = BookedAppointment::for(auth()->user()->id)->past()->with(["appointment", "booker"])->paginate(config('settings.tables_row_count'));
      } else {
        $booked_appointments = BookedAppointment::for(auth()->user()->id)->with(["appointment", "booker"])->paginate(config('settings.tables_row_count'));
      }
    } else {
      $booked_appointments = BookedAppointment::for(auth()->user()->id)->upcoming()->with(["appointment", "booker"])->paginate(config('settings.tables_row_count'));
    }
    return view('dashboard.appointments.index', compact("appointments", "booked_appointments"));
  }

  public function create() {
    $timezonelist = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
    return view('dashboard.appointments.create', compact("timezonelist"));
  }

  public function store(AppointmentRequest $request) {

    $timezone = $request->validated("timezone");

    $appointment = Appointment::create([
      "user_id" => auth()->user()->id,
      "title" => $request->validated("title"),
      "url" => $request->validated("url_slug"),
      "timezone" => $timezone,
      "price" => $request->validated("price"),
      "description" => $request->validated("description"),
      "duration" => $request->validated("duration_minutes"),
    ]);

    $available_days = [];

    foreach ($request->input("available_days") as $key => $day) {
      if (isset($day["status"]) && $day["status"] == "on") {
        foreach ($day["times"] as $t) {
          $from_time = Carbon::createFromFormat('h:i a', $t["from_hour"] . ':' . $t["from_minut"] . ' ' . $t["from_format"], new \DateTimeZone($timezone));
          $to_time = Carbon::createFromFormat('h:i a', $t["to_hour"] . ':' . $t["to_minut"] . ' ' . $t["to_format"], new \DateTimeZone($timezone));

          $available_days[] = [
            "key" => $key,
            "from_date" => $key . 'T' . substr($from_time->toIso8601String(), 11),
            "to_date" => $key . 'T' . substr($to_time->toIso8601String(), 11),
          ];
        }
      }
    }

    $excluded_days = [];

    if ($request->input("excluded_days") != null && $request->input("excluded_days.status") == "on" && $request->input("excluded_days.times") != null) {
      foreach ($request->input("excluded_days.times") as $t) {
        $from_time = Carbon::createFromFormat('h:i a', $t["from_hour"] . ':' . $t["from_minut"] . ' ' . $t["from_format"], new \DateTimeZone($timezone));
        $to_time = Carbon::createFromFormat('h:i a', $t["to_hour"] . ':' . $t["to_minut"] . ' ' . $t["to_format"], new \DateTimeZone($timezone));

        $excluded_days[] = [
          "key" => "excluded",
          "day" => $t["day"],
          "from_date" => substr($from_time->toIso8601String(), 11),
          "to_date" => substr($to_time->toIso8601String(), 11),
        ];
      }
    }

    $appointment->settings()->createMany([...$available_days, ...$excluded_days]);

    return redirect()->route("appointments_manage");
  }

  public function show(Appointment $appointment) {

    if ($appointment->status > 0) {

      $settings = $appointment->settings;
      $appointment->load('author');

      $settings->each(function ($item) {
        $item->makeHidden('appointment_id');
        $item->makeHidden('id');
      });

      $params = [
        "event" => $appointment,
        "settings" => $settings,
      ];

      if ($appointment->price != null && $appointment->price > 0) {
        $stripe = new \Stripe\StripeClient(config("services.stripe.secret"));

        $intent = $stripe->paymentIntents->create([
          'currency' => config("cart.currency_name") ?? "usd",
          'amount' => $appointment->price * 100,
          'automatic_payment_methods' => ['enabled' => true],
          /* =============== if unsigned people can buy stuff =============== */
          'description' => auth()->user()->fullname() . " Booked An Appointment (" . $appointment->title .  ")",
          'metadata' => [
            'product_type' => 'appointment',
            'fullname' => auth()->user()->fullname(),
            'username' => auth()->user()->username,
            'email' => auth()->user()->email,
            'user_id' => auth()->user()->id,
          ],
        ]);
        $params["intent"] = $intent->client_secret;
        $params["intentId"] = $intent->id;
      }

      return view("appointment.booking-appointment")->with($params);
    }

    abort(404);
  }

  public function edit(Appointment $appointment) {
    $settings = $appointment->settings;
    $available = [...$settings->whereIn("key", ["0", "1", "2", "3", "4", "5", "6"])];
    $excluded = [...$settings->where("key", "excluded")];
    $timezonelist = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
    // dd($available, $excluded, $settings);
    return view('dashboard.appointments.create', compact("appointment", "available", "excluded", "timezonelist"));
  }

  public function update(AppointmentRequest $request, Appointment $appointment) {

    $timezone = $request->validated("timezone");

    $appointment->title = $request->validated("title");
    $appointment->url = $request->validated("url_slug");
    $appointment->description = $request->validated("description");
    $appointment->duration = $request->validated("duration_minutes");
    $appointment->timezone = $timezone;

    if ($request->validated("price") != null) {
      $appointment->price = $request->validated("price");
    }

    $appointment->save();

    $available_days = [];

    foreach ($request->input("available_days") as $key => $day) {
      if (isset($day["status"]) && $day["status"] == "on") {
        foreach ($day["times"] as $t) {
          $from_time = Carbon::createFromFormat('h:i a', $t["from_hour"] . ':' . $t["from_minut"] . ' ' . $t["from_format"], new \DateTimeZone($timezone));
          $to_time = Carbon::createFromFormat('h:i a', $t["to_hour"] . ':' . $t["to_minut"] . ' ' . $t["to_format"], new \DateTimeZone($timezone));

          $available_days[] = [
            "key" => $key,
            "from_date" => $key . 'T' . substr($from_time->toIso8601String(), 11),
            "to_date" => $key . 'T' . substr($to_time->toIso8601String(), 11),
          ];
        }
      }
    }

    $excluded_days = [];

    if ($request->input("excluded_days") != null && $request->input("excluded_days.status") == "on" && $request->input("excluded_days.times") != null) {
      foreach ($request->input("excluded_days.times") as $t) {
        $from_time = Carbon::createFromFormat('h:i a', $t["from_hour"] . ':' . $t["from_minut"] . ' ' . $t["from_format"], new \DateTimeZone($timezone));
        $to_time = Carbon::createFromFormat('h:i a', $t["to_hour"] . ':' . $t["to_minut"] . ' ' . $t["to_format"], new \DateTimeZone($timezone));

        $excluded_days[] = [
          "key" => "excluded",
          "day" => $t["day"],
          "from_date" => substr($from_time->toIso8601String(), 11),
          "to_date" => substr($to_time->toIso8601String(), 11),
        ];
      }
    }

    $appointment->settings()->delete();
    $a = $appointment->settings()->createMany([...$available_days, ...$excluded_days]);

    return redirect()->route("appointments_manage");
  }

  public function status(Request $request, Appointment $appointment) {

    $validated = $request->validate([
      "status" => "required|string|in:on,off",
    ]);

    if ($validated["status"] == "on") {
      $appointment->status = 1;
    } else {
      $appointment->status = 0;
    }
    $res = $appointment->save();

    return response()->json(['success' => $res]);
  }

  public function destroy(Appointment $appointment) {
  }
}
