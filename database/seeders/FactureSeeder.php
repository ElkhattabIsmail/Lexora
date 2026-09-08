<?php

namespace Database\Seeders;

use App\Models\Dossier;
use App\Models\Facture;
use Illuminate\Database\Seeder;

class FactureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dossiers = Dossier::with('client')->get();

        if ($dossiers->isEmpty()) {
            return;
        }

        $factureIndex = 1;

        foreach ($dossiers as $dossier) {
            $isPaid = fake()->boolean(60);

            Facture::create([
                'numero_facture' => sprintf('FAC-2026-%04d', $factureIndex++),
                'montant' => fake()->randomFloat(2, 3000, 25000),
                'date_facture' => fake()->dateTimeBetween($dossier->date_ouverture, 'now')->format('Y-m-d'),
                'statut' => $isPaid ? 'Payée' : 'Non payée',
                'client_id' => $dossier->client_id,
                'dossier_id' => $dossier->id,
            ]);
        }
    }
}
