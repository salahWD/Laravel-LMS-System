<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {


  public function up(): void {
    Schema::create('fields', function (Blueprint $table) {
      $table->id();
      $table->foreignId('question_id');

      $table->string("label");
      $table->string("placeholder")->nullable();

      $table->tinyInteger("type");
      $table->integer("order")->nullable();
      $table->boolean("is_required");
      $table->boolean("is_lead_email")->nullable();
      $table->boolean("is_multiple_chooseing")->nullable();
      $table->string("hidden_value")->nullable();
      $table->boolean("format")->nullable();
      $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
      $table->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('fields');
  }
};
