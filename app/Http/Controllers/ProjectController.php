<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /** Liste des projets de l'utilisateur connecté (et seulement les siens). */
    public function index(Request $request): View
    {
        $projects = Project::ownedBy($request->user())   // scope : filtre par propriétaire
            ->with('tasks')                              // eager loading : évite le N+1
            ->latest()
            ->paginate(10);

        return view('projects.index', compact('projects'));
    }

    public function create(): View
    {
        return view('projects.create', ['project' => new Project()]);
    }

    public function store(ProjectRequest $request): RedirectResponse
    {
        // Passer par la relation renseigne automatiquement user_id.
        $project = $request->user()->projects()->create($request->validated());

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Projet créé avec succès.');
    }

    /** Détail d'un projet : tableau des tâches (filtre optionnel ?filter=overdue). */
    public function show(Request $request, Project $project): View
    {
        $filter = $request->query('filter');

        $tasks = $project->tasks()
            ->orderByRaw('deadline IS NULL')   // les tâches sans échéance en dernier
            ->orderBy('deadline')
            ->orderBy('id');

        if ($filter === 'overdue') {
            $tasks->overdue();                 // Local Scope du modèle Task
        }

        return view('projects.show', [
            'project' => $project,
            'tasks' => $tasks->get(),
            'filter' => $filter,
        ]);
    }

    public function edit(Project $project): View
    {
        return view('projects.edit', compact('project'));
    }

    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->validated());

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Projet modifié avec succès.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        // Les tâches sont supprimées en cascade par la base ; on supprime aussi leurs fichiers.
        $paths = $project->tasks()->whereNotNull('attachment_path')->pluck('attachment_path')->all();
        Storage::disk('public')->delete($paths);

        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Projet supprimé.');
    }
}
