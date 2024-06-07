<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up(): void {
    Schema::create('answer_test_entry', function (Blueprint $table) {
      $table->id();
      $table->foreignId('test_entry_id');
      $table->foreignId('answer_id')->nullable();
      $table->string('value')->nullable();
      $table->foreign('test_entry_id')->references('id')->on('test_entries')->onDelete('cascade');
      $table->foreign('answer_id')->references('id')->on('answers')->onDelete('cascade');
    });
  }

  public function down(): void {
    Schema::dropIfExists('answer_test_entry');
  }
};
