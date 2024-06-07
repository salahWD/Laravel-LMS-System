<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {


  public function up(): void {
    Schema::create('comments', function (Blueprint $table) {
      $table->id();
      $table->foreignId('reply_on')->nullable()->references('id')->on('comments')->onDelete('cascade')->onUpdate('cascade');
      $table->foreignId('user_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
      $table->foreignId('article_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
      $table->tinyInteger('approved')->default(0);
      $table->string("text");
      $table->timestamps();
    });
  }

  public function down(): void {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    Schema::dropIfExists('comments');
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
  }
};
