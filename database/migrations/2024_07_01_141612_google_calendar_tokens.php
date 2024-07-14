<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up(): void {
    Schema::create('google_calendar_tokens', function (Blueprint $table) {
      $table->id();
      $table->foreignId("user_id")->constrained("users")->onDelete("cascade");
      $table->text("token");
    });
  }

  public function down(): void {
    Schema::dropIfExists('google_calendar_tokens');
  }
};
