<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use App\Http\Requests\MeetingRequest;
use App\Models\MeetingSetting;

class MeetingController extends Controller {

  public function index() {
    $meetings = Meeting::orderBy("created_at", "DESC")->paginate(15);
    return view('dashboard.meetings.index', compact("meetings"));
  }

  public function create() {
    return view('dashboard.meetings.create');
  }

  public function store(MeetingRequest $request) {

    $meeting = Meeting::create([
      "user_id" => auth()->user()->id,
      "title" => $request->validated("title"),
      "url" => $request->validated("url_slug"),
      "description" => $request->validated("description"),
      "duration" => $request->validated("duration_minutes"),
    ]);

    $available_days = [];

    foreach ($request->input("available_days") as $key => $day) {
      if (isset($day["status"]) && $day["status"] == "on") {
        foreach ($day["times"] as $t) {
          $available_days[] = [
            "key" => $key,
            "value" => $t["from_hour"] . ":" . $t["from_minut"] . ":" . $t["from_format"] . "-" . $t["to_hour"] . ":" . $t["to_minut"] . ":" . $t["to_format"],
          ];
        }
      }
    }

    $excluded_days = [];

    if ($request->input("excluded_days") != null && $request->input("excluded_days.status") == "on" && $request->input("excluded_days.times") != null) {
      foreach ($request->input("excluded_days.times") as $time) {
        $excluded_days[] = [
          "key" => "excluded",
          "value" => $time["day"] . "|" . $time["from_hour"] . ":" . $time["from_minut"] . ":" . $time["from_format"] . "-" . $time["to_hour"] . ":" . $time["to_minut"] . ":" . $time["to_format"],
        ];
      }
    }
    $meeting->settings()->createMany([...$available_days, ...$excluded_days]);

    return redirect()->route("meetings_manage");
  }

  public function show(Meeting $meeting) {
    dd($meeting);
  }

  public function edit(Meeting $meeting) {
    $settings = $meeting->settings;
    $available = [...$settings->whereIn("key", ["0", "1", "2", "3", "4", "5", "6"])];
    $excluded = [...$settings->where("key", "excluded")];
    // dd($available, $excluded, $settings);
    return view('dashboard.meetings.create', compact("meeting", "available", "excluded"));
  }

  public function update(MeetingRequest $request, Meeting $meeting) {

    $meeting->title = $request->validated("title");
    $meeting->url = $request->validated("url_slug");
    $meeting->description = $request->validated("description");
    $meeting->duration = $request->validated("duration_minutes");

    $available_days = [];

    foreach ($request->input("available_days") as $key => $day) {
      if (isset($day["status"]) && $day["status"] == "on") {
        foreach ($day["times"] as $t) {
          $available_days[] = [
            "key" => $key,
            "value" => $t["from_hour"] . ":" . $t["from_minut"] . ":" . $t["from_format"] . "-" . $t["to_hour"] . ":" . $t["to_minut"] . ":" . $t["to_format"],
          ];
        }
      }
    }

    $excluded_days = [];

    if ($request->input("excluded_days") != null && $request->input("excluded_days.status") == "on" && $request->input("excluded_days.times") != null) {
      foreach ($request->input("excluded_days.times") as $time) {
        $excluded_days[] = [
          "key" => "excluded",
          "value" => $time["day"] . "|" . $time["from_hour"] . ":" . $time["from_minut"] . ":" . $time["from_format"] . "-" . $time["to_hour"] . ":" . $time["to_minut"] . ":" . $time["to_format"],
        ];
      }
    }
    $meeting->settings()->delete();
    $meeting->settings()->createMany([...$available_days, ...$excluded_days]);

    return redirect()->route("meetings_manage");
  }

  public function destroy(Meeting $meeting) {
    //
  }
}
