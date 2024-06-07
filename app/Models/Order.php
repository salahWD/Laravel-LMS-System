<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Product;

class Order extends Model {
  use HasFactory;

  public $fillable = [
    "user_id",
    "address",
    "token",
    "client_name",
    "total",
  ];

  protected static function booted() {
    static::creating(function (Order $order) {
      $token = '';
      do {
        $token = self::generateToken();
      } while (self::tokenExists($token));

      $order->token = $token;
    });
  }

  public function user() {
    return $this->belongsTo(User::class);
  }

  public function products() {
    return $this->belongsToMany(Product::class);
  }

  private static function generateToken($length = 9) {
    $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }

  private static function tokenExists($token) {
    return self::where('token', $token)->exists();
  }

  public function status_title() {
    if ($this->stage > 4 || $this->stage < 1) return "Unknown";
    $names = ["ordered", "preparing", "shipping", "done"];
    return $names[$this->stage - 1];
  }

  public function status_class() {
    if ($this->stage > 4 || $this->stage < 1) return "danger";
    $names = ["primary", "secondary", "purple", "success"];
    return $names[$this->stage - 1];
  }
}
