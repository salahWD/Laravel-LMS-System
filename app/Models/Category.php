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
  ];

  public function articles() {
    return $this->hasMany(Article::class);
  }

  public function products() {
    return $this->hasMany(Product::class);
  }

  public function scopeOrdered(Builder $query): void {
    $query->orderBy("order")->orderBy("created_at", "DESC");
  }

  public function scopeNotProduct(Builder $query): void {
    $query->where('is_product_category', false);
  }

  public function is_product() {
    return $this->is_product_category == 1;
  }

  public function get_link() {
    return route("category_show", $this->id);
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
