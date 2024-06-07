<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Report extends Model {
  use HasFactory;

  protected $fillable = [
    "comment_id",
    "violation",
    "admin_id",
  ];

  public function comment() {
    return $this->belongsTo(Comment::class);
  }

  public function admin() {
    return $this->belongsTo(User::class, "admin_id");
  }
}
