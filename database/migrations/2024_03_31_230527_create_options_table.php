<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up(): void {
    Schema::create('options', function (Blueprint $table) {
      $table->id();
      $table->foreignId('field_id');
      $table->string("value");

      $table->foreign('field_id')->references('id')->on('fields')->onDelete('cascade');
    });
  }

  public function down(): void {
    Schema::dropIfExists('options');
  }
};
