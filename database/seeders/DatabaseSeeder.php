<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            ClientSeeder::class,
            DossierSeeder::class,
            AudienceSeeder::class,
            DocumentSeeder::class,
            FactureSeeder::class,
            PaiementSeeder::class,
            HistoriqueSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
