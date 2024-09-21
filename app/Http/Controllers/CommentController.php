<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Validator;
use App\Models\Report;
use App\Models\User;
use App\Models\Article;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CommentController extends Controller {

  // public function __construct() {
  // return $this->middleware('admin')->except(["store"]);
  // }

  public function index() {
  }

  public function store(Request $request, Article $article) {
    $request->validate([
      "comment" => "required|string|max:255",
      "reply" => "nullable|integer|exists:comments,id",
    ]);

    if (!auth()->check()) {
      $request->validate([
        'username' => ['required', 'string', 'max:255', "unique:users,username"],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', "min:3"],
      ]);

      $user = User::create([
        'username' => $request->username,
        'email' => $request->email,
        'password' => Hash::make($request->password),
      ]);

      event(new Registered($user));

      Auth::login($user);
    } else {
      $user = auth()->user();
    }

    $res = Comment::create([
      "article_id" => $article->id,
      "user_id" => $user->id,
      "reply_on" => request("reply"),
      "text" => request("comment")
    ]);

    return ["done" => boolval($user->id && $res), "comment" => [
      "id" => $res->id,
      "fullname" => $res->user->fullname(),
      "user_image" => $res->user->image_url(),
      "date" => $res->created_at->format("Y-m-d h:ma"),
    ]];
  }

  public function report(Request $request, Comment $comment) {
    $validator = Validator::make($request->all(), [
      "violation" => "required|string|max:255",
      "user_id" => "required|exists:users,id",
    ]);

    $user = auth()->user();
    if ($validator->fails()) {
      return $validator->errors();
    }
    if ($user->can_report()) {

      $res = Report::create([
        "violation" => request("violation"),
        "comment_id" => $comment->id,
        "admin_id" => request("user_id"),
      ]);
      return $res;
    }
    return abort(404);
  }

  public function show(Comment $comment) {
  }

  public function edit(Comment $comment) {
  }

  public function update(Request $request, Comment $comment) {
  }

  public function approve(Request $request, Comment $comment) {
    $request->validate([
      "page" => "integer:min:0",
    ]);

    $per_page = 2;
    $next_comment = Comment::orderBy("created_at", "DESC")->where("approved", 0)->skip($per_page * request("page"))->with("user")->first();
    $res = $comment->update(["approved" => 1]);

    if ($next_comment != null) {
      $next_comment = [
        "id" => $next_comment->id,
        "text" => $next_comment->text,
        "username" => $next_comment->user->username,
        "image" => $next_comment->user->image_url(),
        "response_to" => $next_comment->response_to(),
        "created_at" => $next_comment->created_at->format("Y-m-d ga"),
      ];
    }

    return ["result" => $res, "next_comment" => $next_comment];
  }

  public function api_destroy(Request $request, Comment $comment) {
    $request->validate([
      "page" => "integer:min:0",
    ]);

    $per_page = config('settings.tables_row_count'); // comment pagination
    $next_comment = Comment::orderBy("created_at", "DESC")->where("approved", 0)->skip($per_page * request("page"))->with("user")->first();
    $res = $comment->delete();

    if ($next_comment != null) {
      $next_comment = [
        "id" => $next_comment->id,
        "text" => $next_comment->text,
        "username" => $next_comment->user->username,
        "image" => $next_comment->user->image_url(),
        "response_to" => $next_comment->response_to(),
        "created_at" => $next_comment->created_at->format("Y-m-d ga"),
      ];
    }

    return ["result" => $res, "next_comment" => $next_comment];
  }

  public function destroy(Comment $comment) {

    $comment->delete();

    return redirect()->route("comments_manage");
  }
}
