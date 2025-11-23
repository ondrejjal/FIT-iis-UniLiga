<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tournament_contestants_single', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tournament_id')->constrained('tournaments')->onDelete('cascade');
            $table->primary(['user_id', 'tournament_id']);
            $table->boolean('pending');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_contestants_single');
    }
};