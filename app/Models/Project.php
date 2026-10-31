<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    // user_id n'est volontairement pas "fillable" : il est toujours
    // renseigné via la relation $user->projects()->create(...).
    protected $fillable = ['name', 'description'];

    /** Le créateur du projet. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Les tâches du projet. */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /** Scope : ne garde que les projets appartenant à l'utilisateur donné. */
    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }
}
