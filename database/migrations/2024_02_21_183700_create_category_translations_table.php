<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up() {
    Schema::create('category_translations', function (Blueprint $table) {
      $table->id();
      $table->foreignId('category_id');
      $table->string('locale')->index();
      $table->string('title');
      $table->string('description')->nullable();

      $table->unique(['category_id', 'locale']);
      $table->foreign('category_id')->references('id')->on('categories')->onUpdate('cascade')->onDelete('cascade');
    });
  }

  public function down() {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    Schema::dropIfExists('category_translations');
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
  }
};
