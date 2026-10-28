<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function show()
    {
        return inertia('Login');
    }

    public function login(Request $r)
    {
        $data = $r->validate([
            'telephone' => 'required|string',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($data)) {
            return back()->withErrors(['telephone' => 'Numéro ou mot de passe incorrect']);
        }

        $r->session()->regenerate();

        return redirect(Auth::user()->role === 'distributeur' ? '/distributeur' : '/dashboard');
    }
    public function showRegister()
{
    return inertia('Register');
}

public function register(Request $r)
{
    $d = $r->validate([
        'prenom' => 'required|string|max:100',
        'nom' => 'required|string|max:100',
        'telephone' => ['required', 'string', 'regex:/^(77|78|76|70|75)\d{7}$/', 'unique:users,telephone'],
        'password' => 'required|string|min:6|confirmed',
    ]);

    $user = User::create([
        'prenom' => $d['prenom'],
        'nom' => $d['nom'],
        'telephone' => $d['telephone'],
        'password' => $d['password'],
        'role' => 'client', // forcé côté serveur, jamais lu depuis le formulaire
        'solde' => 0,
    ]);

    Auth::login($user);
    $r->session()->regenerate();

    return redirect('/dashboard');
}

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect('/login');
    }
}