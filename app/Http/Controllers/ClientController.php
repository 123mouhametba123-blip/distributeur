<?php

namespace App\Http\Controllers;

use App\Models\{User, Transaction};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller
{
    public function dashboard(Request $r)
    {
        $u = $r->user();

        return inertia('Client/Dashboard', [
            'moi' => $u->id,
            'solde' => $u->solde,
            'transactions' => Transaction::with([
                    'expediteur:id,nom,prenom,telephone',
                    'destinataire:id,nom,prenom,telephone',
                ])
                ->where(fn ($q) => $q->where('expediteur_id', $u->id)->orWhere('destinataire_id', $u->id))
                ->orderByDesc('id')
                ->get(),
        ]);
    }

    public function transferer(Request $r)
    {
        $d = $r->validate([
            'telephone' => 'required|exists:users,telephone',
            'montant' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($d, $r) {
            $from = User::lockForUpdate()->find($r->user()->id);
            $to = User::where('telephone', $d['telephone'])->lockForUpdate()->firstOrFail();

            if ($from->id === $to->id) {
                throw ValidationException::withMessages(['telephone' => ""]);
            }
            if ($from->solde < $d['montant']) {
                throw ValidationException::withMessages(['montant' => 'do']);
            }

            $from->decrement('solde', $d['montant']);
            $to->increment('solde', $d['montant']);

            Transaction::create([
                'type' => 'transfert',
                'montant' => $d['montant'],
                'expediteur_id' => $from->id,
                'destinataire_id' => $to->id,
            ]);
        });

        return back();
    }
}