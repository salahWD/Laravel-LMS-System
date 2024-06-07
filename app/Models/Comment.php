<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Article;

class Comment extends Model {
  use HasFactory;

  protected $fillable = [
    'text',
    'article_id',
    'user_id',
    'reply_on',
    'approved',
  ];

  public function user() {
    return $this->belongsTo(User::class);
  }

  public function article() {
    return $this->belongsTo(Article::class);
  }

  public function replied_to() {
    return $this->belongsTo(Comment::class, "reply_on");
  }

  // returns the text of (responsed_to) comment
  public function response_to() {
    if ($this->reply_on) {
      return $this->replied_to->text;
    } else {
      return '- - -';
    }
  }

  public function section_of_text($length = 75) {
    return substr($this->text, 0, $length) . "...";
  }
}
