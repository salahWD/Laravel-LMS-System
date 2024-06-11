<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up(): void {
    Schema::create('users', function (Blueprint $table) {
      $table->id();
      $table->string('username');
      /* ==== Permissions Table
        0 => desable user
        1 => normal user
        2 => moderator (can remove comments and report report usres and these things)
        3 => full admin
      */
      $table->tinyInteger('permission')->default(1);
      $table->string('image')->nullable();
      $table->string('first_name')->nullable();
      $table->string('last_name')->nullable();
      $table->string('email')->unique();
      $table->string('bio')->nullable();
      // $table->timestamp('email_verified_at')->nullable();
      $table->string('password');
      $table->rememberToken();
      $table->timestamps();
    });
  }

  public function down(): void {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    Schema::dropIfExists('users');
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
  }
};
