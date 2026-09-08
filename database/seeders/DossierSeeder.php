<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Dossier;
use App\Models\User;
use Illuminate\Database\Seeder;

class DossierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $avocats = User::whereHas('role', fn ($q) => $q->where('nom', 'Avocat'))->get();
        $clients = Client::all();

        if ($avocats->isEmpty() || $clients->isEmpty()) {
            return;
        }

        $dossiersData = [
            [
                'numero_dossier' => 'DOS-2026-0001',
                'type_affaire' => 'Droit commercial',
                'statut' => 'En cours',
                'date_ouverture' => '2026-01-10',
                'date_fermeture' => null,
                'archive' => false,
            ],
            [
                'numero_dossier' => 'DOS-2026-0002',
                'type_affaire' => 'Droit du travail',
                'statut' => 'En cours',
                'date_ouverture' => '2026-01-15',
                'date_fermeture' => null,
                'archive' => false,
            ],
            [
                'numero_dossier' => 'DOS-2026-0003',
                'type_affaire' => 'Droit immobilier',
                'statut' => 'Gagné',
                'date_ouverture' => '2025-06-01',
                'date_fermeture' => '2026-02-15',
                'archive' => false,
            ],
            [
                'numero_dossier' => 'DOS-2026-0004',
                'type_affaire' => 'Droit des affaires',
                'statut' => 'En cours',
                'date_ouverture' => '2026-02-01',
                'date_fermeture' => null,
                'archive' => false,
            ],
            [
                'numero_dossier' => 'DOS-2026-0005',
                'type_affaire' => 'Droit civil',
                'statut' => 'Perdu',
                'date_ouverture' => '2025-04-10',
                'date_fermeture' => '2025-11-20',
                'archive' => false,
            ],
            [
                'numero_dossier' => 'DOS-2026-0006',
                'type_affaire' => 'Droit de la famille',
                'statut' => 'Fermé',
                'date_ouverture' => '2025-03-01',
                'date_fermeture' => '2025-09-30',
                'archive' => true,
            ],
            [
                'numero_dossier' => 'DOS-2026-0007',
                'type_affaire' => 'Droit pénal des affaires',
                'statut' => 'En cours',
                'date_ouverture' => '2026-02-15',
                'date_fermeture' => null,
                'archive' => false,
            ],
            [
                'numero_dossier' => 'DOS-2026-0008',
                'type_affaire' => 'Droit commercial',
                'statut' => 'Gagné',
                'date_ouverture' => '2025-08-12',
                'date_fermeture' => '2026-01-20',
                'archive' => false,
            ],
            [
                'numero_dossier' => 'DOS-2026-0009',
                'type_affaire' => 'Droit immobilier',
                'statut' => 'En cours',
                'date_ouverture' => '2026-03-01',
                'date_fermeture' => null,
                'archive' => false,
            ],
            [
                'numero_dossier' => 'DOS-2026-0010',
                'type_affaire' => 'Droit du travail',
                'statut' => 'En cours',
                'date_ouverture' => '2026-03-05',
                'date_fermeture' => null,
                'archive' => false,
            ],
        ];

        foreach ($dossiersData as $index => $data) {
            $client = $clients->get($index % $clients->count());
            $avocat = $avocats->get($index % $avocats->count());

            Dossier::firstOrCreate(
                ['numero_dossier' => $data['numero_dossier']],
                array_merge($data, [
                    'client_id' => $client->id,
                    'avocat_id' => $avocat->id,
                ])
            );
        }

        // Dossiers additionnels via factory
        for ($i = 0; $i < 6; $i++) {
            Dossier::factory()->create([
                'client_id' => $clients->random()->id,
                'avocat_id' => $avocats->random()->id,
            ]);
        }
    }
}
