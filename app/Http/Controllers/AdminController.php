<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Waste;
use App\Models\User;
use Inertia\Inertia;

class AdminController extends Controller
{
    private function getDashboardData($tab)
    {
        $stats = [
            'total_revenue'        => Order::where('status', 'livre')->sum('price'),
            'total_gas_kg'         => Order::where('status', 'livre')->sum('quantity'),
            'total_waste_kg'       => Waste::where('status', 'collecte')->sum('quantity'),
            'active_users'         => User::where('role', 'client')->count(),
            'active_collectors'    => User::whereIn('role', ['ramasseur', 'livreur'])->count(),
            'pending_orders_count' => Order::where('status', 'en_attente')->count(),
            'pending_wastes_count' => Waste::where('status', 'signale')->count(),
        ];

        $recentOrders = Order::with('user', 'collector')->latest()->take(5)->get();
        $recentWastes = Waste::with('user', 'collector')->latest()->take(5)->get();
        $users = User::latest()->get();
        $prices = \App\Models\Price::first();
        
        $markers = [];
        $orders = Order::whereIn('status', ['en_attente', 'en_livraison'])->get();
        foreach($orders as $o) {
            if ($o->latitude && $o->longitude) {
                $markers[] = ['position' => [$o->latitude, $o->longitude], 'label' => 'Cmd Gaz #'.$o->id, 'icon' => 'orange'];
            }
        }
        $wastes = Waste::whereIn('status', ['signale', 'assigne'])->get();
        foreach($wastes as $w) {
            if ($w->latitude && $w->longitude) {
                $markers[] = ['position' => [$w->latitude, $w->longitude], 'label' => 'Déchet #'.$w->id, 'icon' => 'green'];
            }
        }

        return [
            'stats'        => $stats,
            'recentOrders' => $recentOrders,
            'recentWastes' => $recentWastes,
            'users'        => $users,
            'prices'       => $prices,
            'mapMarkers'   => $markers,
            'tab'          => $tab,
        ];
    }

    public function index()
    {
        return Inertia::render('Admin/Dashboard', $this->getDashboardData('overview'));
    }

    public function activity()
    {
        return Inertia::render('Admin/Dashboard', $this->getDashboardData('activity'));
    }

    public function users()
    {
        return Inertia::render('Admin/Dashboard', $this->getDashboardData('users'));
    }

    public function prices()
    {
        return Inertia::render('Admin/Dashboard', $this->getDashboardData('prices'));
    }

    public function map()
    {
        return Inertia::render('Admin/Dashboard', $this->getDashboardData('map'));
    }

    public function updateUserRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:client,ramasseur,livreur,admin',
        ]);

        if ($user->id === auth()->id() && $validated['role'] !== 'admin') {
            return back()->with('error', "Impossible de modifier vos propres droits d'administration.");
        }

        $user->update(['role' => $validated['role']]);

        return back()->with('message', 'Rôle mis à jour avec succès.');
    }

    public function updatePrices(Request $request)
    {
        $validated = $request->validate([
            'gas_price_per_kg'    => 'required|numeric|min:0',
            'waste_reward_per_kg' => 'required|numeric|min:0',
            'delivery_fee'        => 'required|numeric|min:0',
        ]);

        $price = \App\Models\Price::first();
        if ($price) {
            $price->update($validated);
        } else {
            \App\Models\Price::create($validated);
        }

        return back()->with('message', 'Tarifs mis à jour avec succès.');
    }
}
