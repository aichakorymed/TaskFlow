<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_DONE = 'done';

    /** Valeur en base => libellé affiché en français. */
    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'En attente',
        self::STATUS_IN_PROGRESS => 'En cours',
        self::STATUS_DONE => 'Terminée',
    ];

    protected $fillable = ['title', 'status', 'deadline', 'attachment_path'];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
        ];
    }

    /** Le projet auquel appartient la tâche. */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Local Scope : tâches en retard (échéance dépassée et non terminées).
     * Utilisation : Task::overdue()->get() ou $project->tasks()->overdue()->get()
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query
            ->whereNotNull('deadline')
            ->where('deadline', '<', today())
            ->where('status', '!=', self::STATUS_DONE);
    }

    /**
     * Accesseur : statut en français. Utilisation : $task->status_label
     * ("pending" => "En attente").
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? (string) $this->status;
    }

    /** Accesseur : URL publique de la pièce jointe (null s'il n'y en a pas). */
    public function getAttachmentUrlAttribute(): ?string
    {
        // asset() utilise l'URL réelle de la requête (évite les soucis d'APP_URL).
        return $this->attachment_path ? asset('storage/' . $this->attachment_path) : null;
    }

    /** Vrai si la tâche est en retard (même règle que le scope overdue). */
    public function isOverdue(): bool
    {
        return $this->deadline !== null
            && $this->status !== self::STATUS_DONE
            && $this->deadline->lt(today());
    }
}
