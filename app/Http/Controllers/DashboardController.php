<?php

namespace App\Http\Controllers;

use App\Models\Audience;
use App\Models\Client;
use App\Models\Dossier;
use App\Models\Facture;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $closedDossiers = Dossier::whereIn('statut', ['Gagné', 'Perdu', 'Fermé'])->count();
        $wonDossiers = Dossier::where('statut', 'Gagné')->count();

        $stats = [
            'dossiers_actifs' => Dossier::where('statut', 'En cours')->where('archive', false)->count(),
            'audiences_a_venir' => Audience::where('statut', 'Prévue')->where('date', '>=', Carbon::today())->count(),
            'total_clients' => Client::count(),
            'revenus_du_mois' => Facture::where('statut', 'Payée')
                ->whereMonth('date_facture', Carbon::now()->month)
                ->whereYear('date_facture', Carbon::now()->year)
                ->sum('montant'),
            'taux_reussite' => $closedDossiers > 0 ? (int) round(($wonDossiers / $closedDossiers) * 100) : 0,
            'dossiers_par_mois' => Dossier::whereYear('date_ouverture', Carbon::now()->year)
                ->get()
                ->groupBy(fn (Dossier $dossier) => $dossier->date_ouverture?->format('n'))
                ->map->count(),
            'prochaines_audiences' => Audience::with(['dossier.client', 'avocat'])
                ->where('statut', 'Prévue')
                ->where('date', '>=', Carbon::today())
                ->orderBy('date')
                ->orderBy('heure')
                ->limit(5)
                ->get(),
            'derniers_dossiers' => Dossier::with(['client', 'avocat'])
                ->latest()
                ->limit(5)
                ->get(),
        ];

        return view('dashboard', compact('stats'));
    }
}
