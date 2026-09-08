<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'Administrateur',
            'Avocat',
            'Assistant Juridique',
        ];

        foreach ($roles as $nom) {
            Role::firstOrCreate(['nom' => $nom]);
        }
    }
}
