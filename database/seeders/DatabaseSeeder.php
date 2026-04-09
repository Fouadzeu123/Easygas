<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Administrateur (Gère la plate-forme)
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@easygas.com'],
            [
                'name' => 'Admin EasyGas',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'phone' => '600000001',
            ]
        );

        // Ramasseur
        \App\Models\User::firstOrCreate(
            ['email' => 'ramasseur@easygas.com'],
            [
                'name' => 'Jean Ramasseur',
                'password' => bcrypt('password'),
                'role' => 'ramasseur',
                'phone' => '600000002',
            ]
        );

        // Livreur
        \App\Models\User::firstOrCreate(
            ['email' => 'livreur@easygas.com'],
            [
                'name' => 'Paul Livreur',
                'password' => bcrypt('password'),
                'role' => 'livreur',
                'phone' => '691722328',
            ]
        );

        // Client classique
        $client = \App\Models\User::firstOrCreate(
            ['email' => 'client@easygas.com'],
            [
                'name' => 'M. Client',
                'password' => bcrypt('password'),
                'role' => 'client',
                'phone' => '600000003',
            ]
        );


        // Configurer les prix de base s'ils n'existent pas
        if (\App\Models\Price::count() === 0) {
            \App\Models\Price::create([
                'gas_price_per_kg'    => 700,
                'waste_reward_per_kg' => 50,
                'delivery_fee'        => 1000,
            ]);
        }
    }
}
