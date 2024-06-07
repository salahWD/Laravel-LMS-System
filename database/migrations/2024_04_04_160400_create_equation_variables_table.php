<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up(): void {
    Schema::create('equation_variables', function (Blueprint $table) {
      $table->id();
      $table->foreignId("question_id");
      $table->string("title");
      $table->float('min_range')->default(0);
      $table->float('max_range');
      $table->tinyInteger('decimal')->nullable();

      $table->foreign("question_id")->references("id")->on("questions")->onDelete("cascade");
    });
  }

  public function down(): void {
    Schema::dropIfExists('equation_variables');
  }
};
