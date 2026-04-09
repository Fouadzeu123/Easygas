<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Waste;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) return redirect()->route('admin.dashboard');
        if ($user->isStaff()) return redirect()->route('collector.dashboard');

        // Fetch real stats — statuts corrects selon l'enum DB
        $gasOrdered = Order::where('user_id', $user->id)
            ->where('status', 'livre')
            ->sum('quantity');

        $wasteSorted = Waste::where('user_id', $user->id)
            ->where('status', 'collecte')
            ->sum('quantity');

        // CO2 saved: 0.5kg CO2 per kg of waste + 1.2kg per kg of biogas
        $co2Saved = ($wasteSorted * 0.5) + ($gasOrdered * 1.2);

        $stats = [
            [
                'label' => __('Stats.Gas Ordered'),
                'value' => round($gasOrdered, 1) . ' kg',
                'icon'  => 'Flame',
                'color' => 'text-orange-500',
                'bg'    => 'bg-orange-100',
            ],
            [
                'label' => __('Stats.Waste Sorted'),
                'value' => round($wasteSorted, 1) . ' kg',
                'icon'  => 'Trash2',
                'color' => 'text-blue-500',
                'bg'    => 'bg-blue-100',
            ],
            [
                'label' => __('Stats.CO2 Saved'),
                'value' => round($co2Saved, 1) . ' kg',
                'icon'  => 'Leaf',
                'color' => 'text-green-500',
                'bg'    => 'bg-green-100',
            ],
        ];

        // Commandes en cours : statuts conformes à l'enum DB
        $upcomingDeliveries = Order::where('user_id', $user->id)
            ->whereIn('status', ['en_attente', 'confirme', 'en_livraison'])
            ->with('collector')
            ->latest()
            ->get()
            ->map(function ($order) {
                $statusLabels = [
                    'en_attente'   => __('Status.Pending'),
                    'confirme'     => __('Status.Confirmed'),
                    'en_livraison' => __('Status.Delivering'),
                ];
                return [
                    'id'     => $order->id,
                    'type'   => __('Order.Gas', ['quantity' => $order->quantity]),
                    'status' => $statusLabels[$order->status] ?? $order->status,
                    'time'   => $order->updated_at->format('H:i'),
                    'driver' => $order->collector ? $order->collector->name : __('Order.Unassigned'),
                ];
            });

        return Inertia::render('Dashboard', [
            'stats'              => $stats,
            'upcomingDeliveries' => $upcomingDeliveries,
        ]);
    }
}
