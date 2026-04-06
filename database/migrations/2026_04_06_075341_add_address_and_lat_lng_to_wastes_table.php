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
        Schema::table('wastes', function (Blueprint $table) {
            $table->foreignId('collector_id')->nullable()->after('user_id')->constrained('users')->onDelete('set null');
            $table->text('address')->nullable()->after('longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wastes', function (Blueprint $table) {
            $table->dropForeign(['collector_id']);
        });
        Schema::table('wastes', function (Blueprint $table) {
            $table->dropColumn('collector_id');
        });
        Schema::table('wastes', function (Blueprint $table) {
            $table->dropColumn('address');
        });
    }
};
