<?php

namespace App\Http\Controllers;

use App\Models\{User, Transaction};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DistributeurController extends Controller
{
    public function index(Request $r)
{
    return inertia('Distributeur/Index', [
        'solde' => $r->user()->solde,
        'clients' => User::where('role', 'client')->latest()
            ->get(['id', 'nom', 'prenom', 'telephone', 'solde']),
    ]);
}

public function crediter(Request $r)
{
    $d = $r->validate([
        'client_id' => 'required|exists:users,id',
        'montant' => 'required|integer|min:1',
    ]);

    DB::transaction(function () use ($d, $r) {
        $distributeur = User::lockForUpdate()->find($r->user()->id);
        $client = User::where('role', 'client')->lockForUpdate()->findOrFail($d['client_id']);

        if ($distributeur->solde < $d['montant']) {
            throw ValidationException::withMessages(['montant' => 'Solde distributeur insuffisant']);
        }

        $distributeur->decrement('solde', $d['montant']);
        $client->increment('solde', $d['montant']);

        Transaction::create([
            'type' => 'credit',
            'montant' => $d['montant'],
            'expediteur_id' => $distributeur->id,
            'destinataire_id' => $client->id,
        ]);
    });

    return back();
}
}