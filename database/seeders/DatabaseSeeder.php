<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        $this->call(RoleSeeder::class);

        // Crear usuario con rol 1
        $user1 = User::factory()->create([
            'name' => 'User One',
            'email' => 'user1@example.com',
            'password' => Hash::make('123456789'), // Hashear la contraseña
        ]);
        $user1->roles()->attach(1); // Asignar rol 1

        // Crear usuario con rol 2
        $user2 = User::factory()->create([
            'name' => 'Kevin',
            'email' => 'kevin@gmail.com',
            'password' => Hash::make('123456789'), // Hashear la contraseña
        ]);
        $user2->roles()->attach(2); // Asignar rol 2

        // Crear usuario con rol 3
        $user3 = User::factory()->create([
            'name' => 'User Three',
            'email' => 'user3@example.com',
            'password' => Hash::make('123456789'), // Hashear la contraseña
        ]);
        $user3->roles()->attach(3); // Asignar rol 3


        $user4 = User::factory()->create([
            'name' => 'User four',
            'email' => 'user4@example.com',
            'password' => Hash::make('123456789'), // Hashear la contraseña
        ]);
        $user4->roles()->attach(1); // Asignar rol 1
    }
}
