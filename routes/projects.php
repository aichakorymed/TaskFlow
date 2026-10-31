<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes des projets et des tâches
|--------------------------------------------------------------------------
| Ce fichier est chargé dans bootstrap/app.php, à l'intérieur d'un groupe
| ['web', 'auth'] : toutes ces routes exigent donc un utilisateur connecté.
|
| L'alias de middleware "project.owner" (EnsureUserOwnsProject) est aussi
| déclaré dans bootstrap/app.php.
*/

// CRUD des projets : index, create, store, show, edit, update, destroy.
// Pour index/create/store il n'y a pas de {project} : le middleware laisse passer.
Route::resource('projects', ProjectController::class)
    ->middleware('project.owner');

// Tâches imbriquées dans un projet.
// scopeBindings() : {task} est cherchée uniquement parmi les tâches de {project}.
Route::scopeBindings()
    ->middleware('project.owner')
    ->group(function () {
        Route::resource('projects.tasks', TaskController::class)
            ->except(['index', 'show']);

        Route::patch('projects/{project}/tasks/{task}/status', [TaskController::class, 'updateStatus'])
            ->name('projects.tasks.status');
    });
