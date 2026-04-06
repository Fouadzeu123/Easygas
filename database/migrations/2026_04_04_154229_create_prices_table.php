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
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->decimal('gas_price_per_kg', 8, 2)->default(500); // Ex: 500 FCFA/kg
            $table->decimal('waste_reward_per_kg', 8, 2)->default(10); // Ex: 10 pts/kg
            $table->decimal('delivery_fee', 8, 2)->default(1000); // Ex: 1000 FCFA/livraison
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
