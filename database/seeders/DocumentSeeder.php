<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\Dossier;
use App\Models\User;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dossiers = Dossier::all();
        $users = User::all();

        if ($dossiers->isEmpty() || $users->isEmpty()) {
            return;
        }

        $docsList = [
            ['nom' => 'contrat_de_bail_commercial.pdf', 'type' => 'Contrat', 'taille' => 1450000],
            ['nom' => 'assignation_en_justice.pdf', 'type' => 'Plaidoirie', 'taille' => 890000],
            ['nom' => 'conclusions_responsabilite_civile.pdf', 'type' => 'Plaidoirie', 'taille' => 1200000],
            ['nom' => 'releve_compte_bancaire_preuve.pdf', 'type' => 'Preuve', 'taille' => 2400000],
            ['nom' => 'proces_verbal_huissier.pdf', 'type' => 'Preuve', 'taille' => 950000],
            ['nom' => 'jugement_tribunal_instance.pdf', 'type' => 'Jugement', 'taille' => 3100000],
            ['nom' => 'statuts_mise_a_jour.pdf', 'type' => 'Autre', 'taille' => 780000],
            ['nom' => 'mise_en_demeure_avocat.pdf', 'type' => 'Plaidoirie', 'taille' => 540000],
        ];

        foreach ($dossiers as $dossier) {
            // Associer 2 documents aléatoires par dossier
            $selectedDocs = fake()->randomElements($docsList, 2);

            foreach ($selectedDocs as $doc) {
                Document::create([
                    'nom' => $doc['nom'],
                    'chemin' => 'documents/dossiers/'.$dossier->id.'/'.fake()->uuid().'_'.$doc['nom'],
                    'type' => $doc['type'],
                    'taille' => $doc['taille'],
                    'dossier_id' => $dossier->id,
                    'uploaded_by' => $dossier->avocat_id ?? $users->random()->id,
                ]);
            }
        }
    }
}
