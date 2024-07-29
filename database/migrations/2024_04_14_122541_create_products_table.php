<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up(): void {
    Schema::create('products', function (Blueprint $table) {
      $table->id();
      $table->tinyInteger("store")->nullable()->default(1); // manualy => 1 aliexpress => 2 | banggood => 3
      $table->string("product_id")->nullable();
      $table->foreignId("category_id")->nullable();
      $table->string("title");
      $table->text("description")->nullable();
      $table->float("price")->nullable();
      $table->integer("stock")->nullable();
      $table->tinyInteger("type")->default(1); // 1 => dropshipping/stored | 2 => affiliate
      $table->boolean("status")->default(1); // 1 => active
      $table->float("rating")->nullable()->default(0);
      $table->text("images")->nullable();
      $table->timestamps();

      $table->foreign("category_id")->references("id")->on("categories")->onDelete("cascade");
    });
  }

  public function down(): void {
    Schema::dropIfExists('products');
  }
};
