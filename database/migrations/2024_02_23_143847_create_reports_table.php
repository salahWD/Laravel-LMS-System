<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up(): void {
    Schema::create('reports', function (Blueprint $table) {
      $table->id();
      $table->string("violation");
      $table->foreignId('comment_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
      $table->foreignId('admin_id')->constrained("users", "id")->onDelete('cascade')->onUpdate('cascade');
      $table->timestamps();
    });
  }

  public function down(): void {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    Schema::dropIfExists('reports');
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
  }
};
