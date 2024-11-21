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

  public function stages_component($stage) {
    if ($this->stage > $stage) {
      return "<i class=\"text-success text-center\" style=\"width: 40px;\">
          <svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"currentColor\" viewBox=\"0 0 512 512\" height=\"40\">
            <path d=\"M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z\"/>
          </svg>
        </i>";
    } elseif ($this->stage == $stage) {
      return '<button class="btn btn-success text-white"><i class="fa fa-check"></i> done</button>';
    } else {
      return "<i class=\"text-warning text-center\" style=\"width: 40px;\">
          <svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 192 512\" fill=\"currentColor\"  height=\"46\">
            <path d=\"M176 432c0 44.1-35.9 80-80 80s-80-35.9-80-80 35.9-80 80-80 80 35.9 80 80zM25.3 25.2l13.6 272C39.5 310 50 320 62.8 320h66.3c12.8 0 23.3-10 24-22.8l13.6-272C167.4 11.5 156.5 0 142.8 0H49.2C35.5 0 24.6 11.5 25.3 25.2z\"/>
          </svg>
        </i>";
      // return "<i class=\"text-danger text-center\" style=\"width: 40px;\">
      //     <svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 512 512\" fill=\"currentColor\"  height=\"40\">
      //       <path d=\"M367.2 412.5L99.5 144.8C77.1 176.1 64 214.5 64 256c0 106 86 192 192 192c41.5 0 79.9-13.1 111.2-35.5zm45.3-45.3C434.9 335.9 448 297.5 448 256c0-106-86-192-192-192c-41.5 0-79.9 13.1-111.2 35.5L412.5 367.2zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256z\"/>
      //     </svg>
      //   </i>";
    }
  }
}
