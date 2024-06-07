<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up(): void {
    if (!Schema::hasTable('users')) {
      Schema::create('results', function (Blueprint $table) {
        $table->id();
        // $table->string("title");
        // $table->text("description")->nullable();
        $table->foreignId("test_id");
        $table->text("note")->nullable();
        $table->integer("min_score")->nullable();
        $table->tinyInteger("min_correct_questions")->nullable();
        $table->tinyInteger("min_percent")->nullable();
        $table->tinyInteger("max_attempts")->nullable();
        // $table->string("image")->nullable();
        // $table->string("button")->nullable();
        // $table->timestamps();

        $table->foreign("test_id")->references("id")->on("tests")->onDelete("cascade")->onUpdate("cascade");
      });
    }
  }

  public function down(): void {
    Schema::dropIfExists('results');
  }
};
