<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\Request;
use Str;

class TagController extends Controller {

  public function index() {
  }

  public function create() {
  }

  public function store(Request $request) {
  }

  public function show(Tag $tag) {

    return view("tag", [
      "articles" => $tag->articles,
      "tag" => $tag,
    ]);
  }

  public function edit(Tag $tag) {
    $articles = $tag->articles()->orderBy("created_at", "DESC")->limit(5)->get();
    return view('dashboard.tag', compact("tag", "articles"));
  }

  public function update(Request $request, Tag $tag) {
    $request->validate([
      "title" => "required|string|unique:users,username," . $tag->id,
      "slug" => "nullable|string",
      "description" => "nullable|string",
    ]);

    $tag->title = request('title');

    if (request('slug') != null && strlen(request('slug')) > 0) {
      $tag->slug = Str::slug(request('slug'));
    } else {
      $tag->slug = Str::slug(request('title'));
    }
    $tag->description = request('description');

    // save session trigger
    $res = $tag->save();
    $request->session()->flash('tag-saved', $res);
    return redirect()->route("tag_edit", $tag->id);
  }

  public function api_destroy(Request $request, Tag $tag) {
    $request->validate([
      "page" => "integer:min:0",
    ]);

    $per_page = 2;
    $next_tag = Tag::skip($per_page * request("page"))->withCount("articles")->first();
    $res = $tag->delete();

    if ($next_tag != null) {
      $next_tag = [
        "id" => $next_tag->id,
        "title" => $next_tag->title,
        "slug" => $next_tag->slug,
        "description" => $next_tag->description,
        "articles_count" => $next_tag->articles_count,
      ];
    }

    return ["result" => $res, "next_tag" => $next_tag];
  }

  public function destroy(Tag $tag) {

    $tag->delete();

    return redirect()->route("tags_manage");
  }
}
