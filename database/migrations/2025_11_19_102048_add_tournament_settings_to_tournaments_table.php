<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->enum('type', ['individual', 'team'])->default('individual')->after('pending');
            $table->unsignedInteger('max_participants')->default(16)->after('type');
            $table->unsignedInteger('min_participants')->default(2)->after('max_participants');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn(['type', 'max_participants', 'min_participants']);
        });
    }
};
