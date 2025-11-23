<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('tournaments', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained('users');
      $table->boolean('pending');
      $table->string('name', 150);
      $table->date('date');
      $table->time('starting_time');
      $table->text('description')->nullable();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('tournaments');
  }
};