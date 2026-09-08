<?php

namespace Database\Seeders;

use App\Models\Facture;
use App\Models\Paiement;
use Illuminate\Database\Seeder;

class PaiementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $factures = Facture::all();

        if ($factures->isEmpty()) {
            return;
        }

        $modes = ['Virement bancaire', 'Chèque', 'Espèces', 'Carte bancaire'];

        foreach ($factures as $facture) {
            if ($facture->isPayee()) {
                // Créer un paiement complet
                $mode = fake()->randomElement($modes);
                Paiement::create([
                    'montant' => $facture->montant,
                    'date_paiement' => fake()->dateTimeBetween($facture->date_facture, 'now')->format('Y-m-d'),
                    'mode_paiement' => $mode,
                    'reference' => $mode !== 'Espèces' ? 'PAY-'.fake()->bothify('??-#####') : null,
                    'facture_id' => $facture->id,
                ]);
            } elseif (fake()->boolean(40)) {
                // Paiement partiel pour certaines factures non soldées
                $acompte = round(((float) $facture->montant) * 0.4, 2);
                $mode = fake()->randomElement($modes);
                Paiement::create([
                    'montant' => $acompte,
                    'date_paiement' => fake()->dateTimeBetween($facture->date_facture, 'now')->format('Y-m-d'),
                    'mode_paiement' => $mode,
                    'reference' => $mode !== 'Espèces' ? 'PAY-PART-'.fake()->bothify('??-#####') : null,
                    'facture_id' => $facture->id,
                ]);
            }
        }
    }
}
