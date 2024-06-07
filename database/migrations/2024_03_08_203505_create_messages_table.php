<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up(): void {
    Schema::create('messages', function (Blueprint $table) {
      $table->id();
      $table->foreignId("user_id")->nullable();
      $table->string("name")->nullable();
      $table->string("subject");
      $table->string("email")->nullable();
      $table->text("message");
      $table->tinyInteger("is_website")->default(0);
      $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade")->onUpdate("cascade");
      $table->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('messages');
  }
};
