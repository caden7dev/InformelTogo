<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'description',
        'montant',
        'type',
        'date_transaction',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_transaction' => 'date',
        'montant' => 'decimal:2',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec la catégorie
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope pour les recettes
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope pour les dépenses
     */
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    /**
     * Scope pour une période donnée
     */
    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('date_transaction', [$startDate, $endDate]);
    }

    /**
     * Scope pour un utilisateur spécifique
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    /**
 * Relation avec le budget via la catégorie
 */
public function budget()
{
    if (!$this->category_id) {
        return null;
    }

    return Budget::where('category_id', $this->category_id)
        ->where('user_id', $this->user_id)
        ->where('is_active', true)
        ->where('start_date', '<=', $this->date_transaction)
        ->where(function($query) {
            $query->where('end_date', '>=', $this->date_transaction)
                  ->orWhereNull('end_date');
        })
        ->first();
}

/**
 * Événement après la création ou mise à jour
 */
protected static function boot()
{
    parent::boot();

    static::saved(function ($transaction) {
        // Mettre à jour les budgets concernés
        if ($transaction->type === 'expense' && $transaction->category_id) {
            $budget = $transaction->budget();
            if ($budget) {
                $budget->updateCurrentAmount();
                $budget->checkAndSendAlerts();
            }
        }
    });

    static::deleted(function ($transaction) {
        // Mettre à jour les budgets concernés
        if ($transaction->type === 'expense' && $transaction->category_id) {
            $budget = Budget::where('category_id', $transaction->category_id)
                ->where('user_id', $transaction->user_id)
                ->where('is_active', true)
                ->first();
                
            if ($budget) {
                $budget->updateCurrentAmount();
            }
        }
    });
}
}