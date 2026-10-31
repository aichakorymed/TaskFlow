<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Les tâches sont imbriquées dans un projet : /projects/{project}/tasks/...
 * Le middleware "project.owner" garantit que le projet appartient à l'utilisateur,
 * et scopeBindings() garantit que la tâche appartient bien à ce projet.
 */
class TaskController extends Controller
{
    public function create(Project $project): View
    {
        return view('tasks.create', [
            'project' => $project,
            'task' => new Task(['status' => Task::STATUS_PENDING]),
        ]);
    }

    public function store(StoreTaskRequest $request, Project $project): RedirectResponse
    {
        $data = $request->safe()->except('attachment');

        if ($request->hasFile('attachment')) {
            // Stockage sur le disque "public", dans le dossier "attachments".
            $data['attachment_path'] = Storage::disk('public')
                ->putFile('attachments', $request->file('attachment'));
        }

        $project->tasks()->create($data);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Tâche ajoutée avec succès.');
    }

    public function edit(Project $project, Task $task): View
    {
        return view('tasks.edit', compact('project', 'task'));
    }

    public function update(UpdateTaskRequest $request, Project $project, Task $task): RedirectResponse
    {
        $data = $request->safe()->except('attachment');

        if ($request->hasFile('attachment')) {
            $this->deleteAttachment($task); // remplace l'ancien fichier
            $data['attachment_path'] = Storage::disk('public')
                ->putFile('attachments', $request->file('attachment'));
        }

        $task->update($data);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Tâche modifiée avec succès.');
    }

    /** Changement rapide de statut depuis le tableau du projet. */
    public function updateStatus(UpdateTaskStatusRequest $request, Project $project, Task $task): RedirectResponse
    {
        $task->update($request->validated());

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Statut mis à jour.');
    }

    public function destroy(Project $project, Task $task): RedirectResponse
    {
        $this->deleteAttachment($task);
        $task->delete();

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Tâche supprimée.');
    }

    private function deleteAttachment(Task $task): void
    {
        if ($task->attachment_path) {
            Storage::disk('public')->delete($task->attachment_path);
        }
    }
}
