<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\Dossier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['Contrat', 'Plaidoirie', 'Jugement', 'Preuve', 'Autre']);
        $names = [
            'Contrat' => ['contrat_commercial.pdf', 'bail_commercial.pdf', 'protocole_accord.pdf'],
            'Plaidoirie' => ['conclusions_recapitulatives.pdf', 'memoire_defense.pdf', 'assignation.pdf'],
            'Jugement' => ['jugement_tpi.pdf', 'arret_cour_appel.pdf', 'ordonnance_refere.pdf'],
            'Preuve' => ['releve_bancaire.pdf', 'proces_verbal_constat.pdf', 'correspondance_mise_en_demeure.pdf'],
            'Autre' => ['pouvoir_avocat.pdf', 'cin_client.pdf', 'statuts_societe.pdf'],
        ];

        $nom = fake()->randomElement($names[$type]);

        return [
            'nom' => $nom,
            'chemin' => 'documents/'.fake()->uuid().'/'.$nom,
            'type' => $type,
            'taille' => fake()->numberBetween(25600, 5242880), // 25 Ko à 5 Mo
            'dossier_id' => Dossier::factory(),
            'uploaded_by' => User::factory(),
        ];
    }
}
