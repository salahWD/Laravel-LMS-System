<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('order_product', function (Blueprint $table) {
      $table->id();
      $table->foreignId("order_id");
      $table->foreignId("product_id");
      $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('cascade');
      $table->foreign('product_id')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
      $table->integer("quantity")->default(1);
    });
  }

  public function down(): void {
    Schema::dropIfExists('order_product');
  }
};
