<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {


  public function up() {
    Schema::create('test_attempts', function (Blueprint $table) {
      $table->id();
      $table->foreignId("test_id");
      $table->foreignId("user_id");
      $table->tinyInteger('last_step')->default(0);
      $table->boolean('is_done')->default(0);
      $table->timestamps();

      $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade")->onUpdate("cascade");
      $table->foreign("test_id")->references("id")->on("tests")->onDelete("cascade")->onUpdate("cascade");
    });
  }

  public function down() {
    Schema::dropIfExists('test_attempts');
  }
};
