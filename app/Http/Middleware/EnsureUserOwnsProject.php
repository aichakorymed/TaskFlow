<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Refuse (403) l'accès à un projet qui n'appartient pas à l'utilisateur connecté.
 *
 * Enregistré sous l'alias "project.owner" dans bootstrap/app.php.
 */
class EnsureUserOwnsProject
{
    public function handle(Request $request, Closure $next): Response
    {
        // Paramètre {project} de la route : un modèle Project (route model binding)
        // ou, selon l'ordre des middlewares, simplement l'identifiant.
        $project = $request->route('project');

        // Routes sans projet (liste, création) : rien à vérifier.
        if ($project === null) {
            return $next($request);
        }

        if (! $project instanceof Project) {
            $project = Project::findOrFail($project); // 404 si inexistant
        }

        abort_unless(
            $request->user() !== null && (int) $project->user_id === (int) $request->user()->id,
            403,
            "Ce projet ne vous appartient pas."
        );

        return $next($request);
    }
}
