<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up() {
    Schema::create('tests', function (Blueprint $table) {
      $table->id();
      $table->string("title");
      $table->tinyInteger("order")->nullable();
      $table->text("description")->nullable();
      $table->string("thumbnail")->nullable();
      $table->string("intro_image")->nullable();
      $table->boolean("has_intro")->default(TRUE);
      $table->string("intro_title")->nullable()->default("intro title");
      $table->string("intro_description")->nullable()->default("intro description");
      $table->string("intro_btn")->nullable()->default("intro button");
      // $table->foreignId("course_id")->nullable();
      $table->foreignId("certificate_id")->nullable();
      $table->foreignId("result_id")->nullable();
      $table->boolean("status")->default(FALSE);
      $table->boolean("can_go_back")->default(FALSE);
      // $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
      $table->foreign('result_id')->references('id')->on('results')->nullOnDelete();
      $table->foreign('certificate_id')->references('id')->on('certificates')->nullOnDelete();
      $table->timestamps();
    });
    Schema::table("results", function ($table) {
      $table->foreign("test_id")->references("id")->on("tests")->onDelete("cascade")->onUpdate("cascade");
    });
  }

  public function down() {
    Schema::dropIfExists('tests');
  }
};
