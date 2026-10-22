<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@cekdulu.com',
            'password' => Hash::make('password'),
            'role_id' => 3
        ]);

        User::create([
            'name' => 'Tim Verifikasi',
            'email' => 'verifier@cekdulu.com',
            'password' => Hash::make('password'),
            'role_id' => 2
        ]);

        User::create([
            'name' => 'Masyarakat Pelapor',
            'email' => 'user@cekdulu.com',
            'password' => Hash::make('password'),
            'role_id' => 1
        ]);
    }
}