<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
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

  public function update(ProfileUpdateRequest $request): RedirectResponse {

    $user = $request->user();
    $user->first_name = request("first_name");
    $user->last_name = request("last_name");
    $user->bio = request("bio");

    if ($user->isDirty('email')) {
      $user->email_verified_at = null;
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
