<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up() {
    Schema::create('item_user', function (Blueprint $table) {
      $table->id();
      $table->foreignId("student_id");
      $table->foreignId("course_item_id");
      $table->foreign("student_id")->references("id")->on("users")->onDelete("cascade");
      $table->foreign("course_item_id")->references("id")->on("course_items")->onDelete("cascade");
      $table->timestamps();
    });
  }

  public function down() {
    Schema::dropIfExists('item_user');
  }
};
