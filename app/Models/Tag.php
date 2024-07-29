<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;

class Tag extends Model {
  use HasFactory;

  protected $fillable = [
    "title",
    "slug",
  ];

  public function articles() {
    return $this->belongsToMany(Article::class);
  }

  public function scopeMostUsed(Builder $query): void {
    $query->withCount("articles")->orderBy("articles_count");
  }
}
