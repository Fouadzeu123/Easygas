<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('waste_id')->constrained()->onDelete('cascade');
            $table->foreignId('collector_id')->constrained('users')->onDelete('restrict');
            $table->enum('status', ['en_route', 'collecte', 'annule'])->default('en_route');
            $table->timestamp('collected_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
