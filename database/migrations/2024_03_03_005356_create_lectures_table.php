<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {


  public function up(): void {
    Schema::create('lectures', function (Blueprint $table) {
      $table->id();
      $table->string("title");
      $table->text("description")->nullable();
      $table->string("video");
      $table->tinyInteger("status")->default(1);
      $table->tinyInteger("order")->nullable();
      $table->string("thumbnail")->nullable();
      $table->foreignId("user_id");
      // $table->foreignId("course_id");
      $table->timestamps();

      $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
      // $table->foreign('course_id')->references('id')->on('courses')->onUpdate('cascade')->onDelete('cascade');
    });
  }

  public function down(): void {
    Schema::dropIfExists('lectures');
  }
};
