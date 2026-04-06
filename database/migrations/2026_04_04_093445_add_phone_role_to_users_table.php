<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('name');
            $table->enum('role', ['client', 'ramasseur', 'livreur', 'admin'])->default('client')->after('phone');
            $table->string('avatar')->nullable()->after('role');
            $table->decimal('points', 10, 2)->default(0)->after('avatar');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'role', 'avatar', 'points']);
        });
    }
};
