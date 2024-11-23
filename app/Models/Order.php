<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Builder;

class Order extends Model {
  use HasFactory;

  public $fillable = [
    "user_id",
    "address",
    "token",
    "stage",
    "client_name",
    "intent_id",
    "total",
  ];

  public function scopeReal(Builder $query) {
    $query->where("stage", ">", 0);
  }

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
    return $this->belongsToMany(Product::class)->withPivot("quantity");
  }

  public function message() {
    $message = '';
    foreach ($this->products as $pro) {
      $message .= "\n" . $pro->title . " => *" . $pro->pivot?->quantity . '*';
    }
    return $message;
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

  public function calc_price() {
    if ($this->products->count() > 0) {
      $total_price = 0;
      foreach ($this->products as $product) {
        $total_price += $product->pivot?->quantity != null ? $product->price * $product->pivot?->quantity : $product->price;
      }
      return $total_price;
    }
    return 0;
  }

  public function calc_quantity() {
    if ($this->products->count() > 0) {
      $total_qty = 0;
      foreach ($this->products as $product) {
        $total_qty += $product->pivot?->quantity != null ? $product->pivot?->quantity : 1;
      }
      return $total_qty;
    }
    return 0;
  }

  public function get_title() {
    $pr = $this->products->first();
    if ($pr != null) {
      return $pr->title . " and other (" . $this->calc_quantity() . ") products";
    }
    return "Unknown Order";
  }

  public function status_title() {
    if ($this->stage > 4 || $this->stage < 1) return "Unknown";
    $names = ["preparing", "shipping", "delivery", "Receved"];
    return $names[$this->stage - 1];
  }

  public function status_class() {
    if ($this->stage > 4 || $this->stage < 1) return "danger";
    $names = ["primary", "secondary", "purple", "success"];
    return $names[$this->stage - 1];
  }

  public function get_date($details = false) {
    $date = Carbon::parse($this->created_at);
    if ($date != null) {
      return $date->format($details ? "Y/m/d g:i A" : "Y/m/d");
    } else {
      return $this->created_at;
    }
  }
}
