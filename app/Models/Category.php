<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'color',
        'user_id'
    ];

    protected static function boot()
    {
        parent::boot();

        // Assigner automatiquement l'user_id seulement si c'est une catégorie personnelle
        static::creating(function ($category) {
            // Si user_id n'est pas défini et qu'un utilisateur est connecté
            if (is_null($category->user_id) && auth()->check()) {
                $category->user_id = auth()->id();
            }
        });
    }

    /**
     * Scope pour les catégories globales (sans user_id)
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * Scope pour les catégories personnelles
     */
    public function scopePersonal($query, $userId = null)
    {
        $userId = $userId ?: auth()->id();
        return $query->where('user_id', $userId);
    }

    /**
     * Scope pour obtenir toutes les catégories disponibles pour un utilisateur
     */
    public function scopeForUser($query, $userId = null)
    {
        $userId = $userId ?: auth()->id();
        
        return $query->where(function($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhereNull('user_id'); // Inclure les catégories globales
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Vérifier si c'est une catégorie globale
     */
    public function isGlobal(): bool
    {
        return is_null($this->user_id);
    }

    /**
     * Vérifier si c'est une catégorie personnelle
     */
    public function isPersonal(): bool
    {
        return !is_null($this->user_id);
    }
}