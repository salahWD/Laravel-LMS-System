<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up(): void {
    Schema::create('meetings', function (Blueprint $table) {
      $table->id();
      $table->foreignId("user_id");
      $table->string("title");
      $table->string("url");
      $table->text("description")->nullable();
      $table->integer("duration"); // duration in minuts
      $table->integer("buffer_zone")->default(0); // free time (can not be booked) from now()
      $table->float("price")->nullable();
      $table->string("color")->nullable();
      $table->boolean("status")->default(1);
      $table->timestamps();

      $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
    });

    Schema::create('meeting_settings', function (Blueprint $table) {
      $table->id();
      $table->foreignId("meeting_id");
      $table->string("key");
      $table->string("value")->nullable();

      $table->foreign("meeting_id")->references("id")->on("users")->onDelete("cascade");
    });
  }

  public function down(): void {
    Schema::dropIfExists('meeting_settings');
    Schema::dropIfExists('meetings');
  }
};
