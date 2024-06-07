<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up(): void {
    Schema::create('orders', function (Blueprint $table) {
      $table->id();
      $table->foreignId("user_id")->nullable();
      $table->integer("total");
      $table->string("token")->unique();
      $table->string("client_name")->nullable();
      $table->string("client_email")->nullable();
      $table->string("address");
      $table->tinyInteger("stage")->default(1);
      $table->timestamps();
      $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
    });
  }

  public function down(): void {
    Schema::dropIfExists('orders');
  }
};
