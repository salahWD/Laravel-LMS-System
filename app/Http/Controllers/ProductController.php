<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductOffer;
use File;
use Cart;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler; // Import Symfony DomCrawler

class ProductController extends Controller {

  public function __construct() {
    $this->middleware('auth')->only("add_to_cart"); // only loged in users can use this controller
  }

  public function index() {
    $header_slides = ProductOffer::where("featured", 1)->with("product")->limit(3)->get();
    $offers = ProductOffer::where("featured", 0)->with("product")->limit(3)->get();
    $new_products = Product::inRandomOrder()->limit(3)->get();
    $categories = Category::inRandomOrder()->isProduct()->limit(3)->with("translations")->withCount("products")->get();
    $packages = collect([collect([...$new_products, ...$new_products]), collect([...$new_products, ...$new_products])]);

    return view("shop", compact("new_products", "header_slides", "categories", "offers", "packages"));
  }

  public function create() {
    $categories = Category::isProduct()->get();
    $product = new Product();

    return view("dashboard.products.create", compact("categories", "product"));
  }

  public function store(Request $request) {
    $request->validate([
      "product_id" => "sometimes|nullable|string",
      "title" => "required|string",
      "affiliate_link" => "nullable|url",
      "description" => "sometimes|nullable|string",
      "category" => "nullable|integer|exists:categories,id",
      "images" => "nullable|array",
      "images.*" => "required|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte
      "url_images" => "nullable|array",
      "url_images.*" => "required|url",
      // "rating" => [
      //   "nullable",
      //   "min:0",
      //   function ($attribute, $value, $fail) {
      //     if (!is_numeric($value)) {
      //       $fail("The {$attribute} attibute is not a number.");
      //     }
      //   }
      // ],
      "price" => [
        "required_if:product_type,1",
        "min:0",
        function ($attribute, $value, $fail) {
          if (!is_numeric($value)) {
            $fail("The {$attribute} attibute is not a number.");
          }
        }
      ],
      "stock" => "required_if:product_type,1|integer|min:0",
    ]);

    $info = [
      "product_id" => request("product_id"),
      "title" => request("title"),
      "affiliate_link" => request("affiliate_link"),
      "description" => request("description"),
      "type" => request("product_type"),
      "category_id" => request("category"),
      "store" => request("store"),
      // "rating" => request("rating"),
    ];

    if (request("product_type") == 1) {
      $info["price"] = request("price");
      $info["stock"] = request("stock");
    }

    if ($request->hasFile('images')) {
      $images = [];
      // upload images
      foreach ($request->file('images') as $image) {
        $name = date('mdYHis') . uniqid() . substr($image->getClientOriginalName(), -10);
        $image->move(public_path('images/products'), $name);
        $images[] = $name;
      }
      $info["images"] = implode("|", $images);
    }
    if (request('url_images') != null) {
      $url_images = [];
      $info["images"] = '';
      // upload images
      foreach (request('url_images') as $image) {

        $name = date('mdYHis') . uniqid() . substr($image, -10);

        /* curl method */
        $ch = curl_init($image);
        $fp = fopen(public_path('/images/products/' . $name), 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $url_images[] = $name;
      }

      $info["images"] .= implode("|", $url_images);
    }

    Product::create($info);

    return redirect()->route("products_manage");
  }

  public function show(Product $product) {
    $similar_products = Product::inRandomOrder()->limit(3)->get();
    return view("single-product", compact("product", "similar_products"));
  }

  public function add_to_cart(Request $request, Product $product) {

    $request->validate(["quantity" => "required|min:1"]);

    /* =========== Note ===========
      tihs commented code is used when unsigned people can buy from the shopp
      ============================ */

    // if (!auth()->check() && empty(request("cart_id"))) {
    //   $id = substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 9);
    // } elseif (!auth()->check()) { // gust with cart it
    //   $id = request("cart_id");
    // } else {
    //   $id = auth()->user()->id;
    // }

    $id = auth()->user()->id;
    Cart::restore($id);
    Cart::add($product->id, $product->title, request("quantity"), $product->price, ["image" => $product->main_image_url()]);
    Cart::store($id);
    return $id;
  }

  public function edit(Request $request, Product $product) {
    $categories = Category::isProduct()->get();
    // dd($product->get_images());// come
    return view("dashboard.products.create", compact("categories", "product"));
  }

  public function update(Request $request, Product $product) {
    $request->validate([
      "title" => "required|string",
      "description" => "sometimes|nullable|string",
      "category" => "nullable|integer|exists:categories,id",
      "affiliate_link" => "nullable|url",
      "old_images" => "nullable|array",
      "old_images.*" => "required|string",
      "url_images" => "nullable|array",
      "url_images.*" => "required|url",
      "images" => "nullable|array",
      "images.*" => "required|image|mimes:png,jpg,jpeg|max:2048", // max = 2 mega byte
      "selected_image" => "nullable|string|in:new_images," . implode(",", (request("old_images") ?? [])),
      "price" => [
        "required_if:product_type,1",
        "min:0",
        function ($attribute, $value, $fail) {
          if (!is_numeric($value)) {
            $fail("The {$attribute} attibute is not a number.");
          }
        }
      ],
      "stock" => "required_if:product_type,1|integer|min:0",
    ], [
      "selected_image" => "selected thumbnail is invalid."
    ]);

    $product->title = request("title");
    $product->description = request("description");
    $product->affiliate_link = request("affiliate_link");
    $product->type = request("product_type");
    $product->category_id = request("category");

    if (request("product_type") == 1) {
      $product->price = request("price");
      $product->stock = request("stock");
    }

    if (request("product_id") != null) {
      $product->product_id = request("product_id");
    }
    if (request("product_type") == 2 && request("store") != null) {
      $product->store = request("store");
    }
    // if (request("rating") != null) {
    //   $product->rating = request("rating");
    // }

    $images = [];

    if ($request->hasFile('images')) {
      foreach ($request->file('images') as $image) {
        // upload new image
        $name = date('mdYHis') . uniqid() . substr($image->getClientOriginalName(), -10);
        $image->move(public_path('images/products'), $name);
        $images[] = $name;
      }
    }

    $saved_images = [];
    if ($product->get_images()) {
      $old_images = request('old_images') ?? [];
      foreach ($product->get_images() as $image) {
        if (!in_array($image, $old_images)) {
          // delete old image
          $explode = explode("/", $image);
          $name = end($explode);
          if (File::exists(public_path("images/products/$name"))) {
            File::delete(public_path("images/products/$name"));
          }
        } else {
          $key = array_search($image, $old_images);
          $saved_images[$key] = $image;
        }
      }
    }

    if (request('url_images') != null) {

      $url_images = [];
      // upload images
      foreach (request('url_images') as $image) {

        $name = date('mdYHis') . uniqid() . substr($image, -10);

        /* curl method */
        $ch = curl_init($image);
        $fp = fopen(public_path('/images/products/' . $name), 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $key = array_search($image, request("url_images"));
        $url_images[$key] = $name;
      }
      ksort($url_images);
      array_push($images, ...$url_images);
      // $images .= implode("|", $url_images);
    }
    // dd($images, $saved_images);

    if (request("selected_image") != null) {
      if (request("selected_image") == "new_images") {
        // dd($images, array_merge($images, $saved_images));
        $product->images = implode("|", array_merge($images, $saved_images));
      } else {
        ksort($saved_images);
        $product->images = implode("|", array_merge($saved_images, $images));
      }
    }

    $product->save();

    return redirect()->route("products_manage");
  }

  public function proxy(Request $request) {
    $request->validate([
      "url" => "required|url",
    ]);

    // Extract the URL to proxy from the query parameters
    $url = request('url');

    dd($url);
    if (!$url) {
      return response()->json(['error' => 'URL parameter is missing'], 400);
    }

    // Validate if the URL is an AliExpress product page
    if (!$this->isAliExpressUrl($url)) {
      return response()->json(['error' => 'Invalid URL. Only AliExpress product pages are allowed'], 400);
    }

    // Create a new GuzzleHttp client
    $client = new Client();

    try {
      // Make a request to the external website
      $response = $client->get($url);

      // Extract the HTML content from the response
      $html = $response->getBody()->getContents();

      // Parse the HTML using Symfony DomCrawler
      $crawler = new Crawler($html);

      // Find the script tag containing the runParams variable
      $scriptTag = $crawler->filterXPath('//script[contains(text(), "window.runParams")]');
      dd($scriptTag);

      // Extract the JavaScript code containing the runParams variable
      $jsCode = $scriptTag->text();

      $jsCode = preg_replace('/(\bdata\b)/', '"$1"', $jsCode, 1);

      // Extract the value of the runParams variable
      preg_match('/window\.runParams\s*=\s*({.*?});/', $jsCode, $matches);
      $runParamsJson = $matches[1] ?? null;

      // Decode the JSON data
      $runParams = null;
      dd($runParamsJson);
      if (!empty($runParamsJson)) {
        $runParams = json_decode($runParamsJson, true);
        dd($runParams);
        $runParams = $runParams["data"];
        $productInfo = [];

        // product id => productInfoComponent->idStr
        $productInfo["product_id"] = $runParams["productInfoComponent"]["idStr"];

        // product title => html_entity_decode(productInfoComponent->subject);
        $productInfo["title"] = html_entity_decode($runParams["productInfoComponent"]["subject"]);

        // product original price => priceComponent->origPrice->minPrice;
        $productInfo["price"] = $runParams["priceComponent"]["origPrice"]["minPrice"];

        // product discounted price => priceComponent->discountPrice->minActMultiCurrencyPrice;
        $productInfo["discounted_price"] = $runParams["priceComponent"]["discountPrice"]["minActMultiCurrencyPrice"];

        // desc url => productDescComponent->descriptionUrl
        $productInfo["description"] = $runParams["productDescComponent"]["descriptionUrl"];

        // quantity => inventoryComponent->totalAvailQuantity
        $productInfo["quantity"] = $runParams["inventoryComponent"]["totalAvailQuantity"];

        // discount check => promotionComponent->discountPromotion
        if ($runParams["promotionComponent"]["discountPromotion"]) {
          // discount value => promotionComponent->discount
          $productInfo["discoun"] = $runParams["promotionComponent"]["discount"];
        }

        // image list check prop => imageComponent->imageExist
        if ($runParams["imageComponent"]["imageExist"]) {
          // image list [640] => imageComponent->image640PathList
          $productInfo["images"] = $runParams["imageComponent"]["image640PathList"];
        }

        return response()->json($productInfo);
      }

      return response()->json([
        "result" => false
      ]);
    } catch (\Exception $e) {
      // Handle errors
      return response()->json(['error' => 'Failed to fetch data from the external website' . $e], 500);
    }
  }

  public function api_destroy(Request $request, Product $product) {
    $request->validate([
      "page" => "integer:min:0",
    ]);

    if (auth()->user()->is_admin()) {

      $per_page = config('settings.tables_row_count'); // article pagination
      $next_product = Product::orderBy("created_at", "DESC")->skip($per_page * request("page"))->first();
      $res = $product->delete();

      return ["result" => $res, "next_product" => $next_product];
    } else {
      return abort(404);
    }
  }

  private function isAliExpressUrl($url) {
    // Define a regex pattern to match AliExpress product URLs
    $pattern = '/^(https?:\/\/)?(www\.)?aliexpress\.com\/item\/\d+\.html/i';

    // Use preg_match to check if the URL matches the pattern
    return preg_match($pattern, $url);
  }
}
