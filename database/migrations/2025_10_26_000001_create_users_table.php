<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('email', 100)->unique();
            $table->string('password_hash', 255);
            $table->string('first_name', 50);
            $table->string('surname', 50);
            $table->string('phone_number', 14)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};