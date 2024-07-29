<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up(): void {
    Schema::create('product_product_offer', function (Blueprint $table) {
      $table->id();
      $table->foreignId("product_id");
      $table->foreignId("product_offer_id");
      $table->foreign("product_id")->references("id")->on("products")->onDelete("cascade");
      $table->foreign("product_offer_id")->references("id")->on("product_offers")->onDelete("cascade");
      $table->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('product_product_offer');
  }
};
