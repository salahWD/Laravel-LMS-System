<?php

namespace App\Http\Controllers;

use File;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller {

  public function index() {
  }

  public function create() {
    $category = new Category;
    return view('dashboard.category')->with("category", $category);
  }

  public function store(Request $request) {
    $request->validate([
      "title" => "required|string|max:120",
      "description" => "nullable|string",
      "order" => "nullable|integer|min:0|max:5",
      "image" => "required|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte
    ]);
    if (request("title_en") != null) {
      $request->validate([
        "title_en" => "required|string|max:120",
        "description_en" => "nullable|string",
      ]);
    }

    $info = [
      "title" => request("title"),
      "description" => request("description"),
      "order" => request("order"),
    ];

    if (request("title_en") != null) {
      $info["en"]["title"] = request("title_en");
      if (request("description_en") != null) {
        $info["en"]["description"] = request("description_en");
      }
    }

    // upload image
    $info["image"] = date('mdYHis') . uniqid() . substr($request->file('image')->getClientOriginalName(), -10);
    $request->image->move(public_path('images/categories'), $info["image"]);

    $category = Category::create($info);

    $request->session()->flash('category-saved', boolval($category->id));

    return redirect()->route("category_edit", $category->id);
  }

  public function show(Category $category) {
    $articles = $category->articles()->where("status", 2)->translatedIn(app()->getLocale())->paginate(12);
    return view("category", compact("category", "articles"));
  }

  public function collection_show(Category $category) {
    if ($category->is_product()) {
      return "hooooli";
      // return view("", compact("category", "featured_products"));
    } else {
      return abort(404);
    }
  }

  public function collection_create() {
    $category = new Category;
    return view('dashboard.category')->with("category", $category);
  }

  public function edit(Category $category) {
    return view('dashboard.category')->with("category", $category);
  }

  public function update(Request $request, Category $category) {
    $request->validate([
      "title" => "required|string|max:120",
      "description" => "nullable|string",
      "order" => "nullable|integer|min:0|max:5",
      "image" => "nullable|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte
    ]);

    if (request("title_en") != null) {
      $request->validate([
        "title_en" => "required|string|max:120",
        "description_en" => "nullable|string",
      ]);
    }

    $category->title = request("title");
    if (request("description") != null) {
      $category->description = request("description");
    }

    if (request("title_en") != null) {
      $en = $category->translateOrNew("en");
      $en->title = request("title_en");
      if (request("description_en") != null) {
        $en->description = request("description_en");
      }
    }

    if (request("order") != null) {
      $category->order = request("order");
    }

    if ($request->hasFile('image')) {
      // delete old image
      if ($category->image != NULL && File::exists(public_path("images/categories/$category->image"))) {
        File::delete(public_path("images/categories/$category->image"));
      }
      // upload new image
      $category->image = date('mdYHis') . uniqid() . substr($request->file('image')->getClientOriginalName(), -10);
      $request->image->move(public_path('images/categories'), $category->image);
    }

    $category->save();
    // dd();
    $request->session()->flash('category-saved', true);

    return redirect()->route("category_edit", $category->id);
  }

  public function api_destroy(Request $request, Category $category) {
    $request->validate([
      "page" => "integer:min:0",
    ]);

    $per_page = 15; // category pagination
    $next_category = Category::notProduct()->orderBy("created_at", "DESC")->skip($per_page * request("page"))->first();
    $res = $category->delete();

    if ($next_category != null) {
      $next_category = [
        "id" => $next_category->id,
        "title" => $next_category->title,
        "description" => $next_category->description,
        "order" => $next_category->order,
        "image" => $next_category->image_url(),
        "created_at" => $next_category->created_at->format("Y-m-d ga"),
      ];
    }

    return ["result" => $res, "next_category" => $next_category];
  }

  public function destroy(Category $category) {
  }
}
