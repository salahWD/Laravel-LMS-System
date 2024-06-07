<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up(): void {
    Schema::create('course_user', function (Blueprint $table) {
      $table->id();
      $table->foreignId("student_id");
      $table->foreignId("course_id");
      $table->foreign("student_id")->references("id")->on("users")->onDelete("cascade");
      $table->foreign("course_id")->references("id")->on("courses")->onDelete("cascade");
      $table->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('course_user');
  }
};
