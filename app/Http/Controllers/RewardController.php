<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class RewardController extends Controller
{
    public function index()
    {
        return Inertia::render('Rewards', [
            'userPoints' => auth()->user()->points ?? 0,
        ]);
    }

    public function cashout(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:500',
            'phone' => 'required|string',
            'provider' => 'required|in:om,momo'
        ]);

        $user = auth()->user();
        
        // Conversion : 1 point = 10 FCFA (par exemple)
        // Donc pour 1000 FCFA, il faut 100 points
        $pointsNeeded = $request->amount / 10;
        
        if ($user->points < $pointsNeeded) {
            return back()->withErrors(['amount' => 'Vous n\'avez pas assez de points.']);
        }
        
        // Déduction des points
        $user->points -= $pointsNeeded;
        $user->save();
        
        // Ici, on pourrait enregistrer une demande dans une table `reward_withdrawals`
        
        return redirect()->route('rewards.index')->with('message', 'Demande de retrait reçue et en cours de traitement !');
    }
}
