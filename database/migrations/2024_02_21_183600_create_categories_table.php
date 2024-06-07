<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up() {
    Schema::create('categories', function (Blueprint $table) {
      $table->id();
      $table->tinyInteger("order")->nullable();
      // $table->string("slug")->nullable();
      $table->boolean("is_product_category")->default(false);
      $table->string("image")->nullable();
      $table->timestamps();
    });
  }

  public function down() {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    Schema::dropIfExists('categories');
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
  }
};
