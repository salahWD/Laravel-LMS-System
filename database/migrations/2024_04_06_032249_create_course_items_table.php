<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up(): void {
    Schema::create('course_items', function (Blueprint $table) {
      $table->id();
      $table->foreignId("course_id");
      $table->foreignId("itemable_id");
      $table->string("itemable_type");
      $table->integer("order");
      $table->foreign("course_id")->references("id")->on("courses")->onDelete("cascade")->onUpdate("cascade");
      $table->unique(['itemable_type', 'itemable_id']);
      // $table->unique(['course_id', 'order']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('course_items');
  }
};
