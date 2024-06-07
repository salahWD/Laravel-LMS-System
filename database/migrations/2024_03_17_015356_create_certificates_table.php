<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up(): void {
    Schema::create('certificates', function (Blueprint $table) {
      $table->id();
      $table->foreignId("user_id");
      $table->string("title");
      $table->text("description")->nullable();
      $table->float("price")->nullable();
      $table->tinyInteger("status")->default(1);
      $table->string("template")->nullable();
      $table->timestamps();
      $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
    });
  }

  public function down(): void {
    Schema::dropIfExists('certificates');
  }
};
