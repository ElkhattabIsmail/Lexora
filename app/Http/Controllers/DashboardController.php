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
    public function index(): View
    {
        // ---------------------------------------------------------------------
        // Calcul optimisé du taux de réussite en une seule requête SQL avec agrégation conditionnelle :
        // SUM(CASE WHEN statut = 'Gagné' THEN 1 ELSE 0 END) évite d'exécuter plusieurs SELECT COUNT distincts.
        // ---------------------------------------------------------------------
        $statutsClos = Dossier::whereIn('statut', ['Gagné', 'Perdu', 'Fermé'])
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN statut = ? THEN 1 ELSE 0 END) as gagnes', ['Gagné'])
            ->first();

        // ---------------------------------------------------------------------
        // Tableau associatif des indicateurs clés de performance (KPI) :
        // ---------------------------------------------------------------------
        $stats = [
            // -----------------------------------------------------------------
            // Dossiers actifs : affaires en cours et non archivées
            // -----------------------------------------------------------------
            'dossiers_actifs' => Dossier::where('statut', 'En cours')->where('archive', false)->count(),

            // -----------------------------------------------------------------
            // Audiences à venir : audiences au statut 'Prévue' à compter d'aujourd'hui
            // -----------------------------------------------------------------
            'audiences_a_venir' => Audience::where('statut', 'Prévue')->where('date', '>=', Carbon::today())->count(),

            // -----------------------------------------------------------------
            // Nombre total de clients enregistrés dans le cabinet
            // -----------------------------------------------------------------
            'total_clients' => Client::count(),

            // -----------------------------------------------------------------
            // Revenus du mois : somme des factures encaissées durant le mois calendaire courant
            // -----------------------------------------------------------------
            'revenus_du_mois' => Facture::where('statut', 'Payée')
                ->whereMonth('date_facture', Carbon::now()->month)
                ->whereYear('date_facture', Carbon::now()->year)
                ->sum('montant'), // Eloquent/Query Builder aggregate function returns 0 if no records match, so no need for null coalescing.

            // -----------------------------------------------------------------
            // Taux de réussite : pourcentage de dossiers gagnés parmi les dossiers clôturés
            // -----------------------------------------------------------------
            'taux_reussite' => $statutsClos->total > 0
                ? (int) round(((float) $statutsClos->gagnes / (float) $statutsClos->total) * 100)
                : 0,

            // -----------------------------------------------------------------
            // Répartition mensuelle des ouvertures de dossiers pour l'année en cours :
            // pluck('date_ouverture') extrait les dates et countBy regroupe par numéro de mois (1 à 12).
            // -----------------------------------------------------------------
            'dossiers_par_mois' => Dossier::query()
                ->whereYear('date_ouverture', Carbon::now()->year)
                ->pluck('date_ouverture')
                ->countBy(fn (string $date) => (int) substr((string) $date, 5, 2)),

            // -----------------------------------------------------------------
            // Les 5 prochaines audiences avec chargement eager des relations pour éviter les requêtes N+1
            // -----------------------------------------------------------------
            'prochaines_audiences' => Audience::with(['dossier.client', 'avocat'])
                ->where('statut', 'Prévue')
                ->where('date', '>=', Carbon::today())
                ->orderBy('date')
                ->orderBy('heure')
                ->limit(5)
                ->get(),

            // -----------------------------------------------------------------
            // Les 5 derniers dossiers créés avec leur client et leur avocat
            // -----------------------------------------------------------------
            'derniers_dossiers' => Dossier::with(['client', 'avocat'])
                ->latest()
                ->limit(5)
                ->get(),
        ];

        // ---------------------------------------------------------------------
        // Retourne la vue du tableau de bord avec l'ensemble des métriques
        // ---------------------------------------------------------------------
        return view('dashboard', compact('stats'));
    }
}
