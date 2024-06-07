<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('article_translations', function (Blueprint $table) {
      $table->id();
      $table->foreignId('article_id');
      $table->string('locale')->index();
      $table->string('title');
      $table->string('description')->nullable();
      $table->text('content')->nullable();
      $table->unique(['article_id', 'locale']);
      $table->foreign('article_id')->references('id')->on("articles")->onUpdate('cascade')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    Schema::dropIfExists('article_translations');
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
  }
};
