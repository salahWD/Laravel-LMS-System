<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up(): void {
    Schema::create('tags', function (Blueprint $table) {
      $table->id();
      $table->string("title");
      $table->string("slug");
      $table->string("description")->nullable();
      $table->timestamps();
    });
    if (Schema::hasTable('articles')) {
      Schema::create('article_tag', function (Blueprint $table) {
        $table->id();
        $table->foreignId('article_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
        $table->foreignId('tag_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
      });
    }
  }

  public function down(): void {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    Schema::dropIfExists('article_tag');
    Schema::dropIfExists('tags');
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
  }
};
