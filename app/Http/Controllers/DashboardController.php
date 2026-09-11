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
        $statutsClos = Dossier::whereIn('statut', ['Gagné', 'Perdu', 'Fermé'])
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN statut = ? THEN 1 ELSE 0 END) as gagnes', ['Gagné'])
            ->first();

        $stats = [
            'dossiers_actifs' => Dossier::where('statut', 'En cours')->where('archive', false)->count(),
            'audiences_a_venir' => Audience::where('statut', 'Prévue')->where('date', '>=', Carbon::today())->count(),
            'total_clients' => Client::count(),
            'revenus_du_mois' => Facture::where('statut', 'Payée')
                ->whereMonth('date_facture', Carbon::now()->month)
                ->whereYear('date_facture', Carbon::now()->year)
                ->sum('montant'),
            'taux_reussite' => $statutsClos->total > 0
                ? (int) round(((float) $statutsClos->gagnes / (float) $statutsClos->total) * 100)
                : 0,
            'dossiers_par_mois' => Dossier::query()
                ->whereYear('date_ouverture', Carbon::now()->year)
                ->pluck('date_ouverture')
                ->countBy(fn (string $date) => (int) substr((string) $date, 5, 2)),
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
