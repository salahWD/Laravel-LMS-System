<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {

    Schema::create('booked_appointments', function (Blueprint $table) {
      $table->id();
      $table->foreignId('booker_id')->constrained("users")->onDelete("cascade");
      $table->foreignId('appointment_id')->nullable()->constrained("appointments")->nullOnDelete();
      $table->string('secret_key', 60)->nullable();
      $table->dateTime('appointment_date');
      $table->tinyInteger('status')->default(1);
      $table->text('notes')->nullable();
      $table->string('meeting_link')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('booked_appointments');
  }
};
