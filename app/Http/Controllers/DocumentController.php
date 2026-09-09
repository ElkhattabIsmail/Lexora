<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Models\Document;
use App\Models\Dossier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function store(StoreDocumentRequest $request, Dossier $dossier): RedirectResponse
    {
        $fichier = $request->file('fichier');
        $nom = $request->input('nom') ?: $fichier->getClientOriginalName();

        try {
            $chemin = $fichier->store("documents/{$dossier->id}", 'public');
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', 'Impossible d\'enregistrer le fichier sur le serveur.');
        }

        $document = $dossier->documents()->create([
            'nom' => $nom,
            'chemin' => $chemin,
            'type' => $request->input('type', 'Autre'),
            'taille' => $fichier->getSize(),
            'uploaded_by' => $request->user()->id,
        ]);

        $dossier->enregistrerAction("Document téléversé : {$document->nom}", $request->user());

        return redirect()
            ->route('dossiers.show', $dossier)
            ->with('success', 'Le document a été téléversé avec succès.');
    }

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
