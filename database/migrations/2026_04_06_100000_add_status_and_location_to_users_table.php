<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_available')->default(false)->after('role');
            $table->decimal('current_lat', 10, 8)->nullable()->after('is_available');
            $table->decimal('current_lng', 11, 8)->nullable()->after('current_lat');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_available', 'current_lat', 'current_lng']);
        });
    }
};
