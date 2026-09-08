<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entreprises = [
            [
                'nom' => 'Atlas Logistics SARL',
                'prenom' => null,
                'telephone' => '+212 5 22 10 20 30',
                'email' => 'contact@atlaslogistics.ma',
                'adresse' => 'Zone Industrielle Sidi Maârouf, Casablanca',
                'type' => 'Entreprise',
            ],
            [
                'nom' => 'Maroc Finance Solutions SA',
                'prenom' => null,
                'telephone' => '+212 5 22 40 50 60',
                'email' => 'direction@marocfinances.ma',
                'adresse' => 'Boulevard d\'Anfa, Casablanca',
                'type' => 'Entreprise',
            ],
            [
                'nom' => 'Immobilier Du Sud SARL',
                'prenom' => null,
                'telephone' => '+212 5 28 82 12 34',
                'email' => 'contact@immodul-sud.ma',
                'adresse' => 'Avenue Hassan II, Agadir',
                'type' => 'Entreprise',
            ],
            [
                'nom' => 'Tech Innovations Africa',
                'prenom' => null,
                'telephone' => '+212 5 37 77 88 99',
                'email' => 'legal@techinnovations.ma',
                'adresse' => 'Technopolis, Rabat-Salé',
                'type' => 'Entreprise',
            ],
            [
                'nom' => 'Société Textile du Nord SA',
                'prenom' => null,
                'telephone' => '+212 5 39 94 11 22',
                'email' => 'info@textilenord.ma',
                'adresse' => 'Zone Franche de Tanger, Tanger',
                'type' => 'Entreprise',
            ],
        ];

        foreach ($entreprises as $entreprise) {
            Client::firstOrCreate(['nom' => $entreprise['nom']], $entreprise);
        }

        $particuliers = [
            [
                'nom' => 'El Mansouri',
                'prenom' => 'Hassan',
                'telephone' => '+212 6 70 11 22 33',
                'email' => 'hassan.elmansouri@gmail.com',
                'adresse' => '12 Rue Zerktouni, Casablanca',
                'type' => 'Particulier',
            ],
            [
                'nom' => 'Bennani',
                'prenom' => 'Kenza',
                'telephone' => '+212 6 71 22 33 44',
                'email' => 'kenza.bennani@yahoo.fr',
                'adresse' => '45 Avenue de France, Rabat',
                'type' => 'Particulier',
            ],
            [
                'nom' => 'Ouazzani',
                'prenom' => 'Tariq',
                'telephone' => '+212 6 72 33 44 55',
                'email' => 'tariq.ouazzani@gmail.com',
                'adresse' => '8 Rue de Fès, Marrakech',
                'type' => 'Particulier',
            ],
            [
                'nom' => 'Amrani',
                'prenom' => 'Nadia',
                'telephone' => '+212 6 73 44 55 66',
                'email' => 'nadia.amrani@outlook.com',
                'adresse' => '23 Boulevard Mohammed V, Tanger',
                'type' => 'Particulier',
            ],
            [
                'nom' => 'Sabir',
                'prenom' => 'Mohamed',
                'telephone' => '+212 6 74 55 66 77',
                'email' => 'mohamed.sabir@gmail.com',
                'adresse' => '67 Avenue Allal Ben Abdellah, Fès',
                'type' => 'Particulier',
            ],
        ];

        foreach ($particuliers as $particulier) {
            Client::firstOrCreate(['email' => $particulier['email']], $particulier);
        }

        // Clients supplémentaires via factory
        Client::factory()->count(5)->particulier()->create();
        Client::factory()->count(3)->entreprise()->create();
    }
}
