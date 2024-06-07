<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up() {
    Schema::create('certificate_user', function (Blueprint $table) {
      $table->id();
      $table->foreignId("user_id");
      $table->foreignId("certificate_id");
      $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
      $table->foreign("certificate_id")->references("id")->on("certificates")->onDelete("cascade");
      $table->timestamps();
    });
  }

  public function down() {
    Schema::dropIfExists('certificate_user');
  }
};
