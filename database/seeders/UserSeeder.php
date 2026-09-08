<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleAdmin = Role::firstOrCreate(['nom' => 'Administrateur']);
        $roleAvocat = Role::firstOrCreate(['nom' => 'Avocat']);
        $roleAssistant = Role::firstOrCreate(['nom' => 'Assistant Juridique']);

        // 1. Administrateur principal
        User::firstOrCreate(
            ['email' => 'admin@lexora.ma'],
            [
                'nom' => 'Alami',
                'prenom' => 'Karim',
                'telephone' => '+212 6 61 12 34 56',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role_id' => $roleAdmin->id,
            ]
        );

        // 2. Avocats du cabinet
        $avocats = [
            [
                'nom' => 'Berrada',
                'prenom' => 'Youssef',
                'email' => 'avocat.berrada@lexora.ma',
                'telephone' => '+212 6 62 23 45 67',
            ],
            [
                'nom' => 'Benjelloun',
                'prenom' => 'Salma',
                'email' => 'avocat.benjelloun@lexora.ma',
                'telephone' => '+212 6 63 34 56 78',
            ],
            [
                'nom' => 'Tazi',
                'prenom' => 'Omar',
                'email' => 'avocat.tazi@lexora.ma',
                'telephone' => '+212 6 64 45 67 89',
            ],
        ];

        foreach ($avocats as $avocat) {
            User::firstOrCreate(
                ['email' => $avocat['email']],
                array_merge($avocat, [
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'role_id' => $roleAvocat->id,
                ])
            );
        }

        // 3. Assistants juridiques
        $assistants = [
            [
                'nom' => 'Idrissi',
                'prenom' => 'Fatima',
                'email' => 'assistant.idrissi@lexora.ma',
                'telephone' => '+212 6 65 56 78 90',
            ],
            [
                'nom' => 'Chraibi',
                'prenom' => 'Mehdi',
                'email' => 'assistant.chraibi@lexora.ma',
                'telephone' => '+212 6 66 67 89 01',
            ],
        ];

        foreach ($assistants as $assistant) {
            User::firstOrCreate(
                ['email' => $assistant['email']],
                array_merge($assistant, [
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'role_id' => $roleAssistant->id,
                ])
            );
        }

        // 4. Avocats supplémentaires pour les tests
        User::factory()->count(2)->avocat()->create();
    }
}
