<?php

namespace App\Http\Controllers;

use File;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller {

  public function index() {
    //
  }

  public function create() {
    $permissions = (new User)->get_permissions();
    return view('dashboard.user', compact("permissions"));
  }

  public function store(Request $request) {
    $request->validate([
      "firstname" => "nullable|string|max:30",
      "lastname" => "nullable|string|max:30",
      "username" => "required|string|max:30|unique:users,username",
      "email" => "required|email|unique:users,email",
      "password" => "required|string|min:3|max:30",
      "rank" => "required|integer|min:0|max:3",
      "image" => "nullable|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte
    ]);

    $info = [
      "email" => request('email'),
      "permission" => request('rank'),
      "username" => request('username'),
      "first_name" => request('firstname'),
      "last_name" => request('lastname'),
      "password" => \Hash::make(request('password')),
    ];

    if ($request->hasFile('image')) {
      // upload image
      $info["image"] = date('mdYHis') . uniqid() . substr($request->file('image')->getClientOriginalName(), -10);
      $request->image->move(public_path('images/users'), $info["image"]);
    }

    // save session trigger
    $res = User::create($info);
    $request->session()->flash('user-saved', $res);
    return redirect()->route("users_manage");
  }

  public function show(string $id) {
    //
  }

  public function show_meetings(User $user) {
    $meetings = $user->appointments()->active()->get();
    $author = $user;

    return view("profile.offer-meetings", compact("meetings", "author"));
  }

  public function edit(User $user) {
    $permissions = $user->get_permissions();

    return view('dashboard.user', compact(["user", "permissions"]));
  }

  public function update(Request $request, User $user) {
    $request->validate([
      "firstname" => "nullable|string|max:30",
      "lastname" => "nullable|string|max:30",
      "username" => "required|string|max:30|unique:users,username," . $user->id,
      "email" => "required|email|unique:users,email," . $user->id,
      "password" => "nullable|string|min:3|max:30",
      "rank" => "required|integer|min:0|max:3",
      "image" => "nullable|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte
    ]);

    $user->email = request('email');
    $user->permission = request('rank');
    $user->username = request('username');

    if (request("firstname") != null) {
      $user->first_name = request('firstname');
    }
    if (request("lastname") != null) {
      $user->last_name = request('lastname');
    }
    if (request("password") != null) {
      $user->password = \Hash::make(request('password'));
    }

    if ($request->hasFile('image')) {
      // delete old image
      if ($user->image != NULL && File::exists(public_path("images/users/$user->image"))) {
        File::delete(public_path("images/users/$user->image"));
      }
      // upload new image
      $user->image = date('mdYHis') . uniqid() . substr($request->file('image')->getClientOriginalName(), -10);
      $request->image->move(public_path('images/users'), $user->image);
    }

    // save session trigger
    $res = $user->save();
    $request->session()->flash('user-saved', $res);
    return redirect()->route("users_manage");
  }

  public function destroy(string $id) {
    //
  }
}
