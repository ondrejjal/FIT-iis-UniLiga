<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tournament_contestants_single', function (Blueprint $table) {
            $table->integer('order')->default(0)->after('pending');
        });

        Schema::table('tournament_contestants_teams', function (Blueprint $table) {
            $table->integer('order')->default(0)->after('pending');
        });
    }

    public function down(): void
    {
        Schema::table('tournament_contestants_single', function (Blueprint $table) {
            $table->dropColumn('order');
        });

        Schema::table('tournament_contestants_teams', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};