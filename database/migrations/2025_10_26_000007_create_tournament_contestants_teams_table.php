<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('tournament_contestants_teams', function (Blueprint $table) {
      $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
      $table->foreignId('tournament_id')->constrained('tournaments')->onDelete('cascade');
      $table->primary(['team_id', 'tournament_id']);
      $table->boolean('pending');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('tournament_contestants_teams');
  }
};