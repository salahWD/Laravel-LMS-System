<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up(): void {

    Schema::create('appointments', function (Blueprint $table) {

      $table->id();
      $table->foreignId("user_id");
      $table->string("title");
      $table->string("url");
      $table->text("description")->nullable();
      $table->integer("duration"); // duration in minuts
      $table->string("timezone");
      $table->integer("buffer_zone")->default(0); // free time (can not be booked) from now()
      $table->float("price")->nullable();
      $table->string("color")->nullable();
      $table->boolean("status")->default(1);
      $table->boolean("meeting_platform")->default(1); // 1 => google_meeting | 0 => zoom
      $table->boolean("link_google_calendar")->default(0); // 1 => will add event to google calendar wit goole meet link using api | 0 => will not
      $table->timestamps();

      $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
    });
  }

  public function down(): void {
    Schema::dropIfExists('appointments');
  }
};
