<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('matches', function (Blueprint $table) {
      $table->id();
      $table->foreignId('tournament_id')->constrained('tournaments');
      $table->foreignId('team1_id')->nullable()->constrained('teams')->onDelete('set null');
      $table->foreignId('team2_id')->nullable()->constrained('teams')->onDelete('set null');
      $table->foreignId('user1_id')->nullable()->constrained('users')->onDelete('set null');
      $table->foreignId('user2_id')->nullable()->constrained('users')->onDelete('set null');
      $table->foreignId('next_match_id')->nullable()->constrained('matches');
      $table->date('date');
      $table->time('starting_time');
      $table->time('finishing_time')->nullable();
      $table->string('result', 100)->nullable();
      $table->integer('round')->nullable();
      $table->foreignId('winner_user_id')->nullable()->constrained('users');
      $table->foreignId('winner_team_id')->nullable()->constrained('teams');
    });

    // CHECK constraint
    //DB::statement('ALTER TABLE matches ADD CONSTRAINT chk_players_or_teams
    //        CHECK (
    //            (user1_id IS NOT NULL AND user2_id IS NOT NULL AND team1_id IS NULL AND team2_id IS NULL)
    //            OR
    //            (team1_id IS NOT NULL AND team2_id IS NOT NULL AND user1_id IS NULL AND user2_id IS NULL)
    //        )');
  }

  public function down(): void
  {
    Schema::dropIfExists('matches');
  }
};