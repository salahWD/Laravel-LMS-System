<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up(): void {
    Schema::create('test_entries', function (Blueprint $table) {
      $table->id();
      $table->foreignId("attempt_id");
      $table->foreignId("question_id");
      // $table->string("value")->nullable();
      $table->timestamps();

      $table->unique(['attempt_id', 'question_id']);
      $table->foreign('attempt_id')->references('id')->on('test_attempts')->onDelete('cascade');
      $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
    });
  }

  public function down(): void {
    Schema::dropIfExists('test_entries');
  }
};
