# TaskFlow — Gestionnaire de tâches collaboratives

Application Laravel 13 réalisée dans le cadre du test technique stagiaire : gestion de projets et de tâches, avec authentification, sécurité par propriétaire, pièces jointes et tests.

## Fonctionnalités

- Inscription, connexion, déconnexion (Laravel Breeze, stack Blade)
- CRUD des projets, visibles uniquement par leur créateur
- Tâches par projet : titre, statut (en attente / en cours / terminée), échéance, pièce jointe
- Changement rapide du statut depuis le tableau des tâches
- Filtre des tâches en retard
- Tests automatisés (PHPUnit)

## Prérequis

- PHP 8.4 ou plus (extensions `pdo_sqlite`, `mbstring`, `fileinfo`)
- Composer
- Node.js et npm (pour compiler le CSS de Breeze)

## Installation

```bash
git clone <URL_DU_DEPOT>
cd TaskFlow

composer install
cp .env           # sous Windows : copy .env
php artisan key:generate

# Base SQLite : le fichier doit exister avant la migration
touch database/database.sqlite   # sous Windows : type nul > database\database.sqlite

php artisan migrate
php artisan storage:link

npm install
npm run build

php artisan serve
```

Ouvrir ensuite http://127.0.0.1:8000, créer un compte, puis utiliser le lien **Projets** du menu.

La configuration SQLite est déjà dans `.env.example` (`DB_CONNECTION=sqlite`). Aucun mot de passe de base de données n'est nécessaire.

## Tests

```bash
php artisan test
```

Les tests couvrent : la redirection vers le login des visiteurs, la création d'un projet, la validation du titre d'une tâche, l'interdiction d'accéder au projet d'un autre utilisateur (403), l'upload de pièce jointe, le scope des tâches en retard et l'accesseur du statut.

## Structure

| Élément                  | Emplacement                                                                                  |
| ------------------------ | -------------------------------------------------------------------------------------------- |
| Routes projets et tâches | `routes/projects.php` (chargé dans `bootstrap/app.php` avec les middlewares `web` et `auth`) |
| Middleware de sécurité   | `app/Http/Middleware/EnsureUserOwnsProject.php` (alias `project.owner`)                      |
| Contrôleurs              | `app/Http/Controllers/ProjectController.php`, `TaskController.php`                           |
| Validation               | `app/Http/Requests/` (`StoreTaskRequest`, `UpdateTaskRequest`, ...)                          |
| Modèles                  | `app/Models/Project.php`, `Task.php` (scope `overdue`, accesseur `status_label`)             |
| Layout et composant      | `resources/views/layouts/taskflow.blade.php`, `resources/views/components/alert.blade.php`   |

## Choix techniques

**Sécurité à deux niveaux.** Le middleware `EnsureUserOwnsProject` renvoie un 403 si le projet de l'URL n'appartient pas à l'utilisateur connecté. La liste des projets est en plus filtrée par un scope Eloquent (`ownedBy`). Pour les routes imbriquées `/projects/{project}/tasks/{task}`, `scopeBindings()` garantit que la tâche appartient bien au projet de l'URL, sinon la réponse est un 404.

**Routes dans un fichier séparé.** `routes/projects.php` est chargé dans `bootstrap/app.php` via `withRouting(then: ...)`, dans un groupe `web` + `auth`. Toutes les routes du fichier exigent donc un utilisateur connecté, sans répéter le middleware.

**Form Requests.** La validation est sortie des contrôleurs. En cas d'échec, Laravel redirige avec les erreurs et les anciennes valeurs (`old()`). `UpdateTaskRequest` hérite de `StoreTaskRequest` car les règles sont identiques.

**Layout.** Breeze utilise déjà `layouts/app.blade.php` comme composant `<x-app-layout>`. Pour ne pas casser ses pages, `layouts/taskflow.blade.php` enveloppe ce composant et ajoute `@yield('content')`.

**Eager loading.** La liste des projets charge les tâches avec `with('tasks')` pour éviter le problème des requêtes N+1.

**Fichiers.** Les pièces jointes sont stockées avec la façade `Storage` sur le disque `public`, dossier `attachments`. Elles sont supprimées avec leur tâche ou leur projet. Le lien est construit avec `asset()` plutôt que `Storage::url()`, pour ne pas dépendre de `APP_URL`.

**PHP 8.4.** Le projet fonctionne sur PHP 8.4 et plus : types de retour partout, propriétés typées, constantes de classe pour les statuts. Les accesseurs sont écrits dans la syntaxe classique demandée par le sujet ; PHP 8.4 permettrait aussi les _property hooks_ pour ce genre de cas.

## Difficultés rencontrées

_(À adapter avec vos propres mots avant de rendre.)_

- Garantir qu'un utilisateur ne puisse pas modifier la tâche d'un autre via une URL mélangeant son propre projet et la tâche d'autrui : résolu avec `scopeBindings()` et testé.
- Intégrer le layout demandé sans casser les vues de Breeze.
- Configurer SQLite sous Windows (création du fichier `database.sqlite`, `storage:link`).
