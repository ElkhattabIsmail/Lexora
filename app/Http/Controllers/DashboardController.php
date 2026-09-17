<?php

namespace App\Http\Controllers;

use App\Models\Audience;
use App\Models\Client;
use App\Models\Dossier;
use App\Models\Facture;
use Carbon\Carbon;
use Illuminate\View\View;

/**
 * DashboardController — aggregates KPI statistics for the main dashboard view.
 *
 * Route: GET /dashboard  (auth + verified)
 * This is a read-only controller with a single action; no write operations.
 */
class DashboardController extends Controller
{
    /**
     * Builds the stats array for the dashboard and returns the view.
     *
     * Statistics computed:
     *   - dossiers_actifs     : count of open ("En cours") non-archived cases.
     *   - audiences_a_venir   : count of "Prévue" hearings from today onwards.
     *   - total_clients       : total number of clients in the system.
     *   - revenus_du_mois     : sum of payments received this calendar month.
     *   - taux_reussite       : win rate = (cases won / cases closed) × 100.
     *   - dossiers_par_mois   : map of month-number → case count for the current year.
     *   - prochaines_audiences: next 5 upcoming hearings (with dossier, client, avocat).
     *   - derniers_dossiers   : 5 most recently created cases.
     *
     * @return View  dashboard  with: $stats
     */
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
