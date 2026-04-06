<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Waste;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class WasteController extends Controller
{
    public function create()
    {
        return Inertia::render('Waste');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:plastique,verre,metal,papier,organique,electronique,autre',
            'quantity' => 'nullable|numeric|min:5',
            'photo' => 'nullable|image|max:2048', // Max 2MB
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'description' => 'nullable|string',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('wastes', 'public');
        }

        $waste = Waste::create([
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'quantity' => $validated['quantity'] ?? null,
            'photo' => $photoPath,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'description' => $validated['description'],
            'status' => 'signale',
            'points_awarded' => 0, // Sera mis à jour par l'admin ou collecteur
        ]);

        return redirect()->route('dashboard')->with('message', 'Déchets signalés avec succès !');
    }
}
