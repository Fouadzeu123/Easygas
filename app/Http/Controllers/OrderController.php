<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Inertia\Inertia;

class OrderController extends Controller
{
    // Fallback logic if Price model is empty
    private const FALLBACK_BOTTLE_PRICE_PER_KG = 666.67; // 4000/6 or 8000/12


    public function create()
    {
        $prices = \App\Models\Price::first();
        $gasPricePerKg = $prices ? $prices->gas_price_per_kg : 640; // Default or DB

        $sizes = [
            ['label' => 'Petite Bouteille',  'quantity' => 6,    'price' => ceil(6 * $gasPricePerKg)],
            ['label' => 'Grande Bouteille', 'quantity' => 12.5, 'price' => ceil(12.5 * $gasPricePerKg)],
        ];

        return Inertia::render('Order', [
            'bottleSizes' => $sizes,
            'userPoints'  => auth()->user()->points ?? 0,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'quantity'    => 'required|numeric|in:6,12.5',
            'bottle_type' => 'required|string',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'address'     => 'required|string|max:255',
            'notes'       => 'nullable|string',
            'use_points'  => 'nullable|boolean',
        ]);

        $prices = \App\Models\Price::first();
        $gasPricePerKg = $prices ? $prices->gas_price_per_kg : 640;
        
        $totalPrice = ceil($validated['quantity'] * $gasPricePerKg);
        $pointsUsed = 0;
        $user       = auth()->user();

        if ($request->boolean('use_points') && $user->points > 0) {
            // 1 point = 10 FCFA de réduction
            $maxDiscount = $user->points * 10;
            $discount    = min($maxDiscount, $totalPrice);
            $pointsUsed  = (int) ceil($discount / 10);
            $totalPrice  -= $discount;

            $user->decrement('points', $pointsUsed);
        }

        Order::create([
            'user_id'   => $user->id,
            'quantity'  => $validated['quantity'],
            'price'     => $totalPrice,
            'latitude'  => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'address'   => $validated['address'],
            'notes'     => $validated['notes'] ?? null,
            'status'    => 'en_attente',
        ]);

        return redirect()->route('dashboard')->with('message', 'Commande effectuée avec succès !');
    }
}
