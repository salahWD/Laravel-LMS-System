<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Oeder;
use Gloudemans\Shoppingcart\Contracts\Buyable;

class Product extends Model implements Buyable {
  use HasFactory;

  // public $images;

  protected $fillable = [
    "title",
    "description",
    "price",
    "store",
    "rating",
    "type",
    "images",
    "category_id",
    "product_id",
    "stock",
  ];

  /* ======== Buyable implementation ======== */
  public function getBuyableIdentifier($options = null) {
    return $this->id;
  }

  public function getBuyableDescription($options = null) {
    return $this->title;
  }

  public function getBuyablePrice($options = null) {
    return $this->price;
  }

  public function orders() {
    return $this->belongsToMany(Oeder::class);
  }

  public function category() {
    return $this->belongsTo(Category::class)->isProduct();
  }

  public function scopeActive($query) {
    return $query->where("status", 1);
  }

  public function scopeIsAffiliate($query) {
    return $query->where("type", 2);
  }

  public function scopeIsDropshipping($query) {
    return $query->where("type", 1);
  }

  public function get_images($length = null) {
    if ($this->getAttribute("images") != null) {
      $urls = explode("|", $this->getAttribute("images"));
      $return = [];
      if (count($urls) > 0) {
        foreach ($urls as $url) {
          if (str_contains($url, "http")) {
            array_push($return, $url);
          } else {
            array_push($return, url("images/products") . "/" . $url);
          }
        }
        if ($length == null) {
          return $return;
        } else {
          return array_slice($return, 0, $length);
        }
      }
    }
    return false;
  }

  public function main_image_url() {
    if ($this->getAttribute("images") != null) {
      return $this->get_images()[0];
    } else {
      return url("images/products/default-product.svg");
    }
  }

  public function is_affiliate() {
    return $this->type == 2;
  }

  /* === link in main platform or store (for admin or affiliate) */
  public function product_link() {
    if ($this->is_affiliate() && $this->product_id) {
      return "https://aliexpress.com/item/" . $this->product_id . ".html";
    }
    return null;
  }

  /* === link in this website (for users) */
  public function get_link() {
    if ($this->is_affiliate() && $this->product_id) {
      return "https://aliexpress.com/item/" . $this->product_id . ".html";
    } else {
      return route("product_show", $this->id);
    }
  }

  public function is_from_aliexpress() {
    // Define a regex pattern to match AliExpress product URLs
    $pattern = '/^(https?:\/\/)?(www\.)?aliexpress\.com\/item\/\d+\.html/i';

    // Use preg_match to check if the URL matches the pattern
    return preg_match($pattern, $this->product_link()) && $this->store == 2;
  }

  public function max_order_quantity() {
    return $this->stock;
  }

  public function show_price() {
    if ($this->price != null && $this->price > 0) {
      return "$" . $this->price;
    }
    return __("free");
  }

  public function show_long_price() {
    if ($this->price != null && $this->price > 0) {
      return "$" . number_format($this->price, 2, '.', ',');
    }
    return __("free");
  }

  public function show_long_old_price() {
    return "$" . number_format(5, 2, '.', ',');
  }

  public function show_old_price() {
    return "$5";
  }

  public function has_old_price() {
    return false;
  }
}
