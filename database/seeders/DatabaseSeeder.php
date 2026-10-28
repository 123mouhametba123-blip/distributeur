<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nom' => 'Diop', 'prenom' => 'Moussa',
            'telephone' => '770000000', 'password' => 'password',
            'role' => 'distributeur','solde' => 1000000,
        ]);

        User::create([
            'nom' => 'Fall', 'prenom' => 'Awa',
            'telephone' => '780000000', 'password' => 'password',
            'role' => 'client', 'solde' => 10000,
        ]);

        User::create([
            'nom' => 'Ndiaye', 'prenom' => 'Cheikh',
            'telephone' => '760000000', 'password' => 'password',
            'role' => 'client', 'solde' => 5000,
        ]);
    }
}