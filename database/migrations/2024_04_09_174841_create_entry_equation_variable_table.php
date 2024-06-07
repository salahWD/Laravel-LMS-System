<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up(): void {
    Schema::create('entry_equation_variable', function (Blueprint $table) {
      $table->id();
      $table->foreignId("entry_id");
      $table->foreignId("equation_variable_id");
      $table->string("value");

      $table->foreign("entry_id")->references("id")->on("test_entries")->onDelete("cascade");
      $table->foreign("equation_variable_id")->references("id")->on("equation_variables")->onDelete("cascade");
    });
  }

  public function down(): void {
    Schema::dropIfExists('entry_equation_variable');
  }
};
