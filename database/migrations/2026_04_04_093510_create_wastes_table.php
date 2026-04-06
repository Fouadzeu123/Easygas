<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wastes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['plastique', 'verre', 'metal', 'papier', 'organique', 'electronique', 'autre'])->default('autre');
            $table->decimal('quantity', 8, 2)->nullable(); // en kg
            $table->string('photo')->nullable();
            $table->enum('status', ['signale', 'assigne', 'collecte', 'traite'])->default('signale');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('description')->nullable();
            $table->decimal('points_awarded', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wastes');
    }
};
