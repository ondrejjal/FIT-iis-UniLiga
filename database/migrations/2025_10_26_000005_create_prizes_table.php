<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('prizes', function (Blueprint $table) {
      $table->foreignId('tournament_id')->constrained('tournaments')->onDelete('cascade');
      $table->unsignedInteger('prize_index');
      $table->primary(['tournament_id', 'prize_index']);
      $table->text('description')->nullable();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('prizes');
  }
};