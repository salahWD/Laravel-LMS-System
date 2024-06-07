<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {


  public function up(): void {
    Schema::create('courses', function (Blueprint $table) {
      $table->id();
      $table->string("title");
      $table->foreignId("user_id");
      $table->string("description")->nullable();
      $table->integer("order")->nullable();
      $table->tinyInteger("status")->default(1);
      $table->float("price")->nullable();
      $table->string("image")->nullable();
      $table->timestamps();

      $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade")->onUpdate("cascade");
    });
  }

  public function down(): void {
    Schema::dropIfExists('courses');
  }
};
