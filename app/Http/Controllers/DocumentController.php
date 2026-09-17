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
    /**
     * Saves an uploaded file temporarily and dispatches the processing job.
     *
     * @param  StoreDocumentRequest  $request  Validated upload form data.
     *                                         Contains: 'fichier' (UploadedFile), 'nom' (optional), 'type'.
     * @param  Dossier               $dossier  The parent dossier to associate the document with.
     *
     * Steps:
     *   1. Read the uploaded file and display name from the request.
     *   2. Store the file to public disk under tmp/{dossier_id}/.
     *   3. Dispatch TraiterTeleversementDocument (async) to move the file and create the DB record.
     *   4. Redirect to the dossier show page with a success message.
     *
     * @return RedirectResponse  Redirects to dossiers.show.
     */
    public function store(StoreDocumentRequest $request, Dossier $dossier): RedirectResponse
    {
        $fichier = $request->file('fichier');
        $nom = $request->input('nom') ?: $fichier->getClientOriginalName();

        try {
            $cheminTemporaire = $fichier->store("tmp/{$dossier->id}", 'public');
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', 'Impossible d\'enregistrer le fichier sur le serveur.');
        }

        TraiterTeleversementDocument::dispatch(
            dossierId: $dossier->id,
            utilisateurId: $request->user()->id,
            cheminTemporaire: $cheminTemporaire,
            nom: $nom,
            type: $request->input('type', 'Autre'),
        );

        return redirect()
            ->route('dossiers.show', $dossier)
            ->with('success', 'Le document a été téléversé avec succès.');
    }

    /**
     * Logs the deletion, removes the file from disk, and deletes the DB record.
     *
     * @param  Request   $request   Used to get the authenticated user for the activity log.
     * @param  Dossier   $dossier   Parent dossier (for the activity log and redirect).
     * @param  Document  $document  Route-model-bound document to delete.
     *
     * Steps:
     *   1. Log "Document supprimé : {name}" to the dossier's historique.
     *   2. Delete the physical file from the 'public' storage disk.
     *   3. Delete the Document model from the database.
     *   4. Redirect to the dossier show page.
     *
     * @return RedirectResponse  Redirects to dossiers.show.
     */
    public function destroy(Request $request, Dossier $dossier, Document $document): RedirectResponse
    {
        $dossier->enregistrerAction("Document supprimé : {$document->nom}", $request->user());

        Storage::disk('public')->delete($document->chemin);
        $document->delete();

        return redirect()
            ->route('dossiers.show', $dossier)
            ->with('success', 'Le document a été supprimé.');
    }
}
