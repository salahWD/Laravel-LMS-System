<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up(): void {
    Schema::create('field_entries', function (Blueprint $table) {
      $table->id();
      $table->foreignId("entry_id");
      $table->foreignId("field_id");
      $table->string("value")->nullable();
      $table->timestamps();

      $table->unique(['entry_id', 'field_id']);
      $table->foreign('entry_id')->references('id')->on('test_entries')->onDelete('cascade');
      $table->foreign("field_id")->references("id")->on("fields")->onDelete("Cascade");
    });
  }

  public function down(): void {
    Schema::dropIfExists('field_entries');
  }
};
