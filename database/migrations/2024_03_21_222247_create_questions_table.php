<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up() {
    Schema::create('questions', function (Blueprint $table) {
      $table->id();
      $table->string("title");
      $table->text("description")->nullable();
      $table->string("button_label")->nullable();
      $table->foreignId("test_id");
      $table->foreign('test_id')->references('id')->on('tests')->onDelete('cascade');
      $table->tinyinteger("type");
      $table->boolean("is_multi_select")->default(0);
      $table->string("image")->nullable();
      $table->string("video")->nullable();
      $table->boolean("is_skippable")->nullable();
      $table->integer("order");
    });
  }

  public function down() {
    Schema::dropIfExists('questions');
  }
};
