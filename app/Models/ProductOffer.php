<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class ProductOffer extends Model {
  use HasFactory;

  protected $fillable = [
    "title",
    "description",
    "discount",
    "featured",
    "is_percentage",
    "expiration_date",
  ];

  protected $casts = [
    'expiration_date' => 'datetime',
  ];

  public function product() {
    return $this->belongsToMany(Product::class)->limit(1);
  }

  public function products() {
    return $this->belongsToMany(Product::class);
  }

  public function image_url() {
    if ($this->image != null) {
      return $this->image;
    } elseif ($this->product?->first()?->main_image_url() != null) {
      return $this->product?->first()?->main_image_url();
    } else {
      return url("images/products/default-product.svg");
    }
  }

  public function show_price() {
    if ($this->products != null) {
      $price = 0;
      foreach ($this->products as $prod) {
        $price += $prod->price;
      }
      if ($this->discount != null && $this->is_percentage != true) {
      } else if ($this->discount != null && $this->is_percentage == true) {
        return $price - (($this->discount / 100) * $price);
      } else {
        return $price;
      }
    } else {
      return url("images/products/default-product.svg");
    }
  }
}
