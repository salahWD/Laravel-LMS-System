<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Certificate;
use App\Models\BookedAppointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use File;

class ProfileController extends Controller {

  public function edit(Request $request): View {
    return view('profile.edit', [
      'user' => auth()->user(),
    ]);
  }

  public function settings(Request $request) {
    return view('profile.settings', [
      'user' => auth()->user(),
    ]);
  }

  public function certificates(Request $request) {

    $certificates = auth()->user()->certificates;

    return view('profile.certificates', [
      'user' => auth()->user(),
      'certificates' => $certificates,
    ]);
  }

  public function meetings(Request $request) {

    $meetings = BookedAppointment::where("booker_id", auth()->user()->id)
      // ->where("status", 1)
      // ->where("appointment_date", ">", now())
      ->with("appointment.author")
      ->get();

    $meetings->map(function ($item) {
      if (auth()->user()->timezone != null) {
        $item->appointment_date->timezone(auth()->user()->timezone);
      }
      return $item;
    });

    $edit_meetings_data = collect([
      ...$meetings->map(function ($item) {
        return [
          "id" => $item->id,
          "title" => $item->appointment->title,
          "notes" => $item->notes,
          "duration" => $item->appointment->duration,
          "date" => $item->appointment_date,
          "author" => $item->appointment->author->fullname(),
        ];
      })
    ]);

    [$active_meetings, $canceled_meetings] = $meetings->partition(function ($i) {
      return $i->status == 1;
    });

    [$meetings, $past_meetings] = $active_meetings->partition(function ($i) {
      return $i->appointment_date >= now();
    });

    return view('profile.meetings', [
      'user' => auth()->user(),
      'meetings' => $meetings,
      'past_meetings' => $past_meetings,
      'canceled_meetings' => $canceled_meetings,
      'edit_meetings_data' => $edit_meetings_data,
    ]);
  }

  public function update(ProfileUpdateRequest $request): RedirectResponse {

    $user = $request->user();
    $user->first_name = request("first_name");
    $user->last_name = request("last_name");
    $user->email = request("email");
    $user->bio = request("bio");

    // if ($user->isDirty('email')) {
    //   $user->email_verified_at = null;
    // }
    if (request("delete_image") == true) {
      // delete old image
      if ($user->image != NULL && File::exists(public_path("images/users/$user->image"))) {
        File::delete(public_path("images/users/$user->image"));
      }
      $user->image = NULL;
    }
    if ($request->hasFile('profile_pic')) {
      // delete old image
      if ($user->image != NULL && File::exists(public_path("images/users/$user->image"))) {
        File::delete(public_path("images/users/$user->image"));
      }
      // upload new image
      $user->image = date('mdYHis') . uniqid() . substr($request->file('profile_pic')->getClientOriginalName(), -10);
      $request->profile_pic->move(public_path('images/users'), $user->image);
    }

    $user->save();

    return Redirect::route('profile.edit')->with('status', 'profile-updated');
  }

  public function destroy(Request $request): RedirectResponse {
    $request->validateWithBag('userDeletion', [
      'password' => ['required', 'current_password'],
    ]);

    $user = $request->user();

    Auth::logout();

    $user->delete();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return Redirect::to('/');
  }
}
