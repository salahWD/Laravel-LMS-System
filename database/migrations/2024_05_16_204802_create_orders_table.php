<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

  public function up(): void {
    Schema::create('orders', function (Blueprint $table) {
      $table->id();
      $table->foreignId("user_id")->nullable();
      $table->integer("total")->default(0);
      $table->string("token")->unique();
      $table->string("client_name")->nullable();
      $table->string("client_email")->nullable();
      $table->string("intent_id")->nullable();
      $table->string("address");
      /*
          1 => paid order and ready to be Processed (Processing)
          2 => Done Processing and is being shipped to your country or city (shipping)
          3 => arraived and is being delivered to the exact address (delivering)
          4 => customer received the order and rated it (received)
      */
      $table->tinyInteger("stage")->default(1);
      $table->timestamps();
      $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
    });
  }

  public function down(): void {
    Schema::dropIfExists('orders');
  }
};
