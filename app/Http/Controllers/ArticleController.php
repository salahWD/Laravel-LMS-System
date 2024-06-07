<?php

namespace App\Http\Controllers;

use File;
use App\Models\Article;
use App\Models\Tag;
use App\Models\Category;
use App\Models\Comment;
use Str;
use Validator;
use Illuminate\Http\Request;

class ArticleController extends Controller {

  public function index() {
  }

  public function create() {
    $categories = Category::notProduct()->all();
    $article = new Article;
    return view('dashboard.article', compact("article", "categories"));
  }

  public function store(Request $request) {
    $request->validate([
      "title" => "required|string|max:120",
      "description" => "nullable|string",
      "status" => "required|integer|min:0|max:2",
      "category" => "nullable|exists:categories,id",
      "tags" => "nullable|string",
      "image" => "required|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte
      "content" => "required",
    ]);

    if (request("title_en") != null) {
      $request->validate([
        "title_en" => "required|string|max:120",
        "description_en" => "nullable|string",
        "content_en" => "required",
      ]);
    }

    $tags = array_map(function ($item) {
      $item = trim($item);
      $item = stripslashes($item);
      return strtolower($item);
    }, explode(",", request("tags")));

    $info = [];
    $info["status"] = request("status");
    $info["comment_status"] = boolval(request("comment_status")) ? 2 : 0;
    $info["user_id"] = auth()->user()->id;
    $info["category_id"] = request("category");
    $info["ar"]["title"] = request("title");
    $info["ar"]["description"] = request("description");
    $info["ar"]["content"] = request("content");

    if (request("title_en") != null) {
      $info["en"]["title"] = request("title_en");
      $info["en"]["content"] = request("content_en");
      if (request("description_en") != null) {
        $info["en"]["description"] = request("description_en");
      }
    }

    if ($request->hasFile('image')) {
      // upload image
      $info["image"] = date('mdYHis') . uniqid() . substr($request->file('image')->getClientOriginalName(), -10);
      $request->image->move(public_path('images/articles'), $info["image"]);
    }

    $article = Article::create($info);

    $tags_ids = Tag::whereIn('title', $tags)->pluck('id', 'title')->all();

    $new_tags_titles = array_unique(array_diff($tags, array_keys($tags_ids)));
    $new_tags = array();

    foreach ($new_tags_titles as $tag) {
      $new_tag = [
        "title" => $tag,
        "slug" => Str::slug($tag),
      ];
      array_push($new_tags, $new_tag);
    }

    $article->tags()->sync($tags_ids);
    $article->tags()->createMany($new_tags);


    $request->session()->flash('article-saved', boolval($article->id));

    return redirect()->route("article_edit", $article->id);
  }

  public function upload_attachment(Request $request) {
    $validator = Validator::make($request->all(), [
      "file" => "required|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte
      // "article_id" => "required|integer|exists:articles,id",
    ]);

    if ($validator->fails()) {
      return $validator->errors();
    }

    // create image name
    $image_name = date('mdYHis') . uniqid() . substr($request->file('file')->getClientOriginalName(), -10);
    // upload new image
    $request->file('file')->move(public_path('images/articles'), $image_name);

    return url("images/articles/$image_name");
  }

  public function show($article_id) {
    $article = Article::where('id', $article_id)->where('status', ">", 0)->withTranslation()->first();
    $comments = Comment::where("article_id", $article->id)->orderBy("created_at", "DESC")->limit(20)->get();
    if ($article) {
      return view('article', compact("article", "comments"));
    } else {
      abort(404);
    }
  }

  public function edit(Article $article) {
    $categories = Category::notProduct()->all();
    return view('dashboard.article', compact("article", "categories"));
  }

  public function update(Request $request, Article $article) {
    $request->validate([
      "title" => "required|string|max:120",
      "description" => "nullable|string",
      "content" => "required",
      "status" => "required|integer|min:0|max:2",
      "category" => "nullable|integer|exists:categories,id",
      "tags" => "nullable|string",
      "image" => "nullable|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte
    ]);
    if (request("title_en") != null) {
      $request->validate([
        "title_en" => "required|string|max:120",
        "description_en" => "nullable|string",
        "content_en" => "required",
      ]);
    }

    $tags = array_map(function ($item) {
      $item = trim($item);
      $item = stripslashes($item);
      return strtolower($item);
    }, explode(",", request("tags")));

    $article->title = request("title");
    $article->content = request("content");
    if (request("description") != null) {
      $article->description = request("description");
    }

    if (request("title_en") != null) {
      $article->translateOrNew("en")->title = request("title_en");
      $article->translateOrNew("en")->content = request("content_en");
      if (request("description_en") != null) {
        $article->translateOrNew("en")->description = request("description_en");
      }
    }

    $article->status = request("status");
    $article->comment_status = boolval(request("comment_status")) ? 2 : 0;

    if (request("category") != null) {
      $article->category()->associate(request("category"));
    }

    $tags_ids = Tag::whereIn('title', $tags)->pluck('id', 'title')->all();

    $new_tags_titles = array_unique(array_diff($tags, array_keys($tags_ids)));
    $new_tags = array();

    foreach ($new_tags_titles as $tag) {
      $new_tag = [
        "title" => $tag,
        "slug" => Str::slug($tag),
      ];
      array_push($new_tags, $new_tag);
    }

    $article->tags()->sync($tags_ids);
    $article->tags()->createMany($new_tags);

    if ($request->hasFile('image')) {
      // delete old image
      if ($article->image != NULL && File::exists(public_path("images/articles/$article->image"))) {
        File::delete(public_path("images/articles/$article->image"));
      }
      // upload new image
      $article->image = date('mdYHis') . uniqid() . substr($request->file('image')->getClientOriginalName(), -10);
      $request->image->move(public_path('images/articles'), $article->image);
    }

    $res = $article->save();
    $request->session()->flash('article-saved', $res);

    return redirect()->route("article_edit", $article->id);
  }

  public function api_destroy(Request $request, Article $article) {
    $request->validate([
      "page" => "integer:min:0",
    ]);

    $per_page = 15; // article pagination
    $next_article = Article::orderBy("created_at", "DESC")->skip($per_page * request("page"))->withCount("comments")->first();
    $res = $article->delete();

    if ($next_article != null) {
      $next_article = [
        "id" => $next_article->id,
        "title" => $next_article->title,
        "description" => $next_article->description,
        "category_id" => $next_article->category_id,
        "category_title" => $next_article->category?->title,
        "comments_count" => $next_article->comments_count,
        "order" => $next_article->order,
        "image" => url("images/articles/" . $next_article->image_url()),
        "status_name" => $next_article->status_name(),
        "status_icon" => $next_article->status_icon(),
        "date" => $next_article->created_at->format("Y-m-d ga"),
      ];
    }

    return ["result" => $res, "next_article" => $next_article];
  }

  public function destroy(Article $article) {
  }
}
