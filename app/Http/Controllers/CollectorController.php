<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Waste;
use Inertia\Inertia;

class CollectorController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $stats = [
            'total_collectes'  => Waste::where('collector_id', $user->id)->where('status', 'collecte')->count(),
            'total_livraisons' => Order::where('collector_id', $user->id)->where('status', 'livre')->count(),
            'total_kg'         => Waste::where('collector_id', $user->id)->where('status', 'collecte')->sum('quantity'),
            // Simplified gain (1000 per delivery + 50 per kg waste)
            'gains'            => (Order::where('collector_id', $user->id)->where('status', 'livre')->count() * 1000) + 
                                  (Waste::where('collector_id', $user->id)->where('status', 'collecte')->sum('quantity') * 50),
        ];

        return Inertia::render('Collector/Dashboard', [
            'role'  => $user->role,
            'stats' => $stats,
        ]);
    }

    public function missions()
    {
        $user = auth()->user();

        // Pending missions (Available for everyone)
        $pendingOrders = Order::where('status', 'en_attente')->with('user')->latest()->get();
        $pendingWastes = Waste::where('status', 'signale')->with('user')->latest()->get();

        // Active missions (Assigned to this specific user)
        $activeOrders = Order::whereIn('status', ['en_livraison', 'confirme'])
                             ->where('collector_id', $user->id)
                             ->with('user')
                             ->latest()->get();
        $activeWastes = Waste::whereIn('status', ['assigne', 'collecte'])
                             ->where('collector_id', $user->id)
                             ->with('user')
                             ->latest()->get();

        return Inertia::render('Collector/Missions', [
            'role'          => $user->role,
            'pendingOrders' => $pendingOrders,
            'pendingWastes' => $pendingWastes,
            'activeOrders'  => $activeOrders,
            'activeWastes'  => $activeWastes,
        ]);
    }

    public function map(Request $request)
    {
        return Inertia::render('Collector/Map', [
            'role' => auth()->user()->role,
            'target' => $request->only(['lat', 'lng', 'address'])
        ]);
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:confirme,en_livraison,livre,annule',
        ]);

        if ($validated['status'] === 'en_livraison' && $order->collector_id && $order->collector_id !== auth()->id()) {
            return back()->with('error', 'Cette mission est déjà prise par un autre agent.');
        }

        $order->update([
            'status'       => $validated['status'],
            'collector_id' => auth()->id(),
        ]);

        return back()->with('message', 'Statut de la commande mis à jour.');
    }

    public function updateWasteStatus(Request $request, Waste $waste)
    {
        $validated = $request->validate([
            'status'        => 'required|in:assigne,collecte,traite',
            'quantity_real' => 'nullable|numeric|min:0.1',
        ]);

        if ($validated['status'] === 'assigne' && $waste->collector_id && $waste->collector_id !== auth()->id()) {
            return back()->with('error', 'Cette collecte est déjà prise par un autre agent.');
        }

        $updateData = [
            'status'       => $validated['status'],
            'collector_id' => auth()->id(),
        ];


        // Lors de la collecte définitive, on enregistre le poids réel et on attribue les points
        if ($validated['status'] === 'collecte' && isset($validated['quantity_real'])) {
            $updateData['quantity']       = $validated['quantity_real'];
            $updateData['points_awarded'] = ceil($validated['quantity_real'] * 10);

            // Créditer l'utilisateur
            $waste->user->increment('points', $updateData['points_awarded']);
        }

        $waste->update($updateData);

        return back()->with('message', 'Statut de la collecte mis à jour.');
    }

    public function updateAvailability(Request $request)
    {
        $validated = $request->validate([
            'is_available' => 'required|boolean',
        ]);

        auth()->user()->update(['is_available' => $validated['is_available']]);

        return back()->with('message', $validated['is_available'] ? 'Vous êtes maintenant en ligne.' : 'Vous êtes maintenant hors ligne.');
    }

    public function updateLocation(Request $request)
    {
        $validated = $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        auth()->user()->update([
            'current_lat' => $validated['lat'],
            'current_lng' => $validated['lng'],
        ]);

        return response()->json(['message' => 'Localisation mise à jour.']);
    }
}
