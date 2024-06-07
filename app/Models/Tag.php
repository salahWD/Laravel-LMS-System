<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Article;

class Tag extends Model {
  use HasFactory;

  protected $fillable = [
    "title",
    "slug",
  ];

  public function articles() {
    return $this->belongsToMany(Article::class);
  }
}
