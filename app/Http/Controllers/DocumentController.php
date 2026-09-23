<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Jobs\TraiterTeleversementDocument;
use App\Models\Document;
use App\Models\Dossier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * DocumentController — handles file uploads and deletions for a dossier.
 *
 * Upload flow (asynchronous):
 *   1. store() saves the raw file to a tmp/ directory.
 *   2. store() dispatches TraiterTeleversementDocument to a queue worker.
 *   3. The job moves the file, creates the Document record, and logs the action.
 *
 * Routes (nested under /dossiers/{dossier}/documents/):
 *   POST   /dossiers/{dossier}/documents          → store()
 *   DELETE /dossiers/{dossier}/documents/{document} → destroy()
 */
class DocumentController extends Controller
{
    public function store(StoreDocumentRequest $request, Dossier $dossier): RedirectResponse
    {
        // ---------------------------------------------------------------------
        // Récupère l'objet UploadedFile depuis la requête multipart
        // ---------------------------------------------------------------------
        $fichier = $request->file('fichier');

        // ---------------------------------------------------------------------
        // Si aucun nom personnalisé n'est fourni, on conserve le nom de fichier d'origine
        // ---------------------------------------------------------------------
        $nom = $request->input('nom') ?: $fichier->getClientOriginalName();

        // ---------------------------------------------------------------------
        // Écriture physique temporaire du fichier sur le disque 'public' dans storage/app/public/tmp/{id}
        // ---------------------------------------------------------------------
        try {
            $cheminTemporaire = $fichier->store("tmp/{$dossier->id}", 'public');
        } catch (\Throwable $e) {
            // Journalise l'exception dans laravel.log sans faire planter l'application
            report($e);

            return back()
                ->with('error', 'Impossible d\'enregistrer le fichier sur le serveur.');
        }

        // ---------------------------------------------------------------------
        // TraiterTeleversementDocument::dispatch(...) :
        // Dépile le traitement lourd vers le worker en arrière-plan (Queue) :
        // déplacement définitif du fichier, extraction des métadonnées et création de la ligne Document.
        // ---------------------------------------------------------------------
        TraiterTeleversementDocument::dispatch(
            dossierId: $dossier->id,
            utilisateurId: $request->user()->id,
            cheminTemporaire: $cheminTemporaire,
            nom: $nom,
            type: $request->input('type', 'Autre'),
        );

        // ---------------------------------------------------------------------
        // Réponse HTTP immédiate sans faire attendre l'utilisateur pendant le traitement
        // ---------------------------------------------------------------------
        return redirect()
            ->route('dossiers.show', $dossier)
            ->with('success', 'Le document a été téléversé avec succès.');
    }

    public function destroy(Request $request, Dossier $dossier, Document $document): RedirectResponse
    {
        // ---------------------------------------------------------------------
        // Enregistre la suppression dans l'historique d'audit du dossier
        // ---------------------------------------------------------------------
        $dossier->enregistrerAction("Document supprimé : {$document->nom}", $request->user());

        // ---------------------------------------------------------------------
        // Suppression physique du fichier sur le disque 'public' (storage/app/public/...)
        // ---------------------------------------------------------------------
        Storage::disk('public')->delete($document->chemin);

        // ---------------------------------------------------------------------
        // Suppression de la ligne dans la table 'documents'
        // ---------------------------------------------------------------------
        $document->delete();

        // ---------------------------------------------------------------------
        // Redirection vers le dossier avec message flash
        // ---------------------------------------------------------------------
        return redirect()
            ->route('dossiers.show', $dossier)
            ->with('success', 'Le document a été supprimé.');
    }
}
