<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up(): void {
    Schema::create('product_offers', function (Blueprint $table) {
      $table->id();
      $table->string("title");
      $table->string("description")->nullable();
      $table->string("image")->nullable();
      $table->boolean("featured")->default(0);
      $table->integer("discount")->nullable();
      $table->boolean("is_percentage")->default(1);
      $table->timestamp("expiration_date")->nullable();
      $table->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('product_offers');
  }
};
