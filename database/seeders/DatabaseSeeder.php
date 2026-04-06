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
                'phone' => '600000004',
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

        // Missions de test pour le livreur (Orders)
        $livreur = \App\Models\User::where('email', 'livreur@easygas.com')->first();
        if ($livreur) {
            \App\Models\Order::firstOrCreate(
                ['user_id' => $client->id, 'status' => 'en_livraison'],
                [
                    'collector_id' => $livreur->id,
                    'quantity' => 12.5,
                    'price' => 6500,
                    'latitude' => 4.0615,
                    'longitude' => 9.7860,
                    'address' => 'Bonamoussadi, Carrefour Bijou, Douala',
                ]
            );
        }

        // Missions de test pour le ramasseur (Wastes)
        $ramasseur = \App\Models\User::where('email', 'ramasseur@easygas.com')->first();
        if ($ramasseur) {
            \App\Models\Waste::firstOrCreate(
                ['user_id' => $client->id, 'status' => 'assigne'],
                [
                    'collector_id' => $ramasseur->id,
                    'type' => 'plastique',
                    'quantity' => 5,
                    'latitude' => 4.0450,
                    'longitude' => 9.7010,
                    'address' => 'Akwa, Rue de l’Hôpital, Douala',
                ]
            );
        }

        // Configurer les prix de base s'ils n'existent pas
        if (\App\Models\Price::count() === 0) {
            \App\Models\Price::create([
                'gas_price_per_kg'    => 500,
                'waste_reward_per_kg' => 50,
                'delivery_fee'        => 1000,
            ]);
        }
    }
}
