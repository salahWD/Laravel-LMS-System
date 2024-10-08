<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Category extends Model implements TranslatableContract {
  use Translatable;

  public $translatedAttributes = ['title', 'description'];
  protected $fillable = [
    'image',
    'order',
    'is_product_category'
  ];

  public function articles() {
    return $this->hasMany(Article::class);
  }

  public function products() {
    return $this->hasMany(Product::class);
  }

  public function show_date() {
    if ($this->created_at != null) {
      return $this->created_at->format('Y-m-d');
    } else {
      return $this->created_at;
    }
  }

  public function scopeOrdered(Builder $query): void {
    $query->orderBy("order")->orderBy("created_at", "DESC");
  }

  public function scopeMostUsed(Builder $query): void {
    $query->withCount("articles")->orderBy("articles_count");
  }

  public function scopeNotProduct(Builder $query) {
    return $query->where('is_product_category', false);
  }

  public function is_product() {
    return $this->is_product_category == 1;
  }

  public function get_link() {
    return route("category_show", $this->id);
  }


  public function get_prod_link() {
    return route("product_category_show", $this->id);
  }

  public function scopeIsProduct(Builder $query): void {
    $query->where('is_product_category', true);
  }

  public function image_url() {
    if ($this->image) {
      return url("images/categories/$this->image");
    } else {
      return url("images/categories/default-category.svg");
    }
  }
}
