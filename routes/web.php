<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return inertia('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Client routes
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/order', [\App\Http\Controllers\OrderController::class, 'create'])->name('order.create');
    Route::post('/order', [\App\Http\Controllers\OrderController::class, 'store'])->name('order.store');
    
    Route::get('/waste', [\App\Http\Controllers\WasteController::class, 'create'])->name('waste.create');
    Route::post('/waste', [\App\Http\Controllers\WasteController::class, 'store'])->name('waste.store');

    Route::get('/rewards', [\App\Http\Controllers\RewardController::class, 'index'])->name('rewards.index');
    Route::post('/rewards/cashout', [\App\Http\Controllers\RewardController::class, 'cashout'])->name('rewards.cashout');

    Route::get('/activity', function () {
        $user = auth()->user();
        if ($user->isStaff()) {
            return inertia('Activity', [
                'orders' => $user->missionsDelivered()->latest()->get()->map(fn ($o) => [
                    'id'         => $o->id,
                    'type'       => 'gaz',
                    'title'      => 'Livraison Gaz #' . $o->id,
                    'date'       => $o->created_at->diffForHumans(),
                    'status'     => match($o->status) {
                        'livre'        => 'Livré',
                        'annule'       => 'Annulée',
                        default        => ucfirst($o->status),
                    },
                    'amount'     => '+' . number_format($o->price * 0.1, 0, ',', ' ') . ' FCFA (Commission)', // Example 10%
                    'isPositive' => true,
                ]),
                'wastes' => $user->missionsCollected()->latest()->get()->map(fn ($w) => [
                    'id'         => $w->id,
                    'type'       => 'recyclage',
                    'title'      => 'Collecte #' . $w->id,
                    'date'       => $w->created_at->diffForHumans(),
                    'status'     => match($w->status) {
                        'collecte' => 'Collecté',
                        'traite'   => 'Traité',
                        default    => ucfirst($w->status),
                    },
                    'amount'     => '+' . ($w->quantity * 50) . ' FCFA (Gain)',
                    'isPositive' => true,
                ]),
            ]);
        }
        return inertia('Activity', [
            'orders' => $user->orders()->latest()->get()->map(fn ($o) => [
                'id'         => $o->id,
                'type'       => 'gaz',
                'title'      => 'Recharge ' . $o->quantity . 'kg',
                'date'       => $o->created_at->diffForHumans(),
                'status'     => match($o->status) {
                    'en_attente'   => 'En attente',
                    'confirme'     => 'Confirmée',
                    'en_livraison' => 'En livraison',
                    'livre'        => 'Livré',
                    'annule'       => 'Annulée',
                    default        => ucfirst($o->status),
                },
                'amount'     => '-' . number_format($o->price, 0, ',', ' ') . ' FCFA',
                'isPositive' => false,
            ]),
            'wastes' => $user->wastes()->latest()->get()->map(fn ($w) => [
                'id'         => $w->id,
                'type'       => 'recyclage',
                'title'      => 'Collecte ' . ucfirst($w->type),
                'date'       => $w->created_at->diffForHumans(),
                'status'     => match($w->status) {
                    'signale'  => 'Signalé',
                    'assigne'  => 'Assigné',
                    'collecte' => 'Collecté',
                    'traite'   => 'Traité',
                    default    => ucfirst($w->status),
                },
                'amount'     => '+' . $w->points_awarded . ' pts',
                'isPositive' => true,
            ]),
        ]);
    })->name('activity');

    Route::get('/profile', function () {
        return inertia('Profile', [
            'user' => auth()->user()->load(['orders', 'wastes']),
        ]);
    })->name('profile');

    // Staff (Livreurs & Ramasseurs) routes
    Route::middleware('role:livreur,ramasseur')->prefix('staff')->name('collector.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\CollectorController::class, 'index'])->name('dashboard');
        Route::get('/missions', [\App\Http\Controllers\CollectorController::class, 'missions'])->name('missions');
        Route::get('/map', [\App\Http\Controllers\CollectorController::class, 'map'])->name('map');
        Route::patch('/order/{order}/status', [\App\Http\Controllers\CollectorController::class, 'updateOrderStatus'])->name('order.update');
        Route::patch('/waste/{waste}/status', [\App\Http\Controllers\CollectorController::class, 'updateWasteStatus'])->name('waste.update');
        Route::patch('/availability', [\App\Http\Controllers\CollectorController::class, 'updateAvailability'])->name('availability.update');
        Route::post('/location', [\App\Http\Controllers\CollectorController::class, 'updateLocation'])->name('location.update');
    });

    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'index'])->name('dashboard');
        Route::get('/activity', [\App\Http\Controllers\AdminController::class, 'activity'])->name('activity');
        Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('users');
        Route::get('/prices', [\App\Http\Controllers\AdminController::class, 'prices'])->name('prices');
        Route::get('/map', [\App\Http\Controllers\AdminController::class, 'map'])->name('map');
        
        Route::patch('/users/{user}/role', [\App\Http\Controllers\AdminController::class, 'updateUserRole'])->name('users.updateRole');
        Route::patch('/prices', [\App\Http\Controllers\AdminController::class, 'updatePrices'])->name('prices.update');
    });
});

require __DIR__.'/settings.php';
