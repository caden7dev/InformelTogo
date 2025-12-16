<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'description',
        'period',
        'amount',
        'current_amount',
        'start_date',
        'end_date',
        'is_active',
        'has_alert',
        'alert_threshold',
        'notification_settings'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'has_alert' => 'boolean',
        'alert_threshold' => 'integer',
        'notification_settings' => 'array'
    ];

    protected $appends = [
        'progress_percentage',
        'remaining_amount',
        'days_remaining',
        'is_over_budget',
        'status'
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec la catégorie
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relation avec les transactions
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'category_id', 'category_id')
            ->whereBetween('date_transaction', [$this->start_date, $this->end_date ?? now()])
            ->where('type', 'expense');
    }

    /**
     * Calculer le pourcentage de progression
     */
    public function getProgressPercentageAttribute(): float
    {
        if ($this->amount == 0) {
            return 0;
        }
        
        $percentage = ($this->current_amount / $this->amount) * 100;
        return min(100, round($percentage, 2));
    }

    /**
     * Montant restant
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->amount - $this->current_amount);
    }

    /**
     * Jours restants
     */
    public function getDaysRemainingAttribute(): int
    {
        $endDate = $this->end_date ?? $this->start_date->endOfMonth();
        return max(0, now()->diffInDays($endDate, false));
    }

    /**
     * Vérifier si le budget est dépassé
     */
    public function getIsOverBudgetAttribute(): bool
    {
        return $this->current_amount > $this->amount;
    }

    /**
     * Statut du budget
     */
    public function getStatusAttribute(): string
    {
        if ($this->is_over_budget) {
            return 'over_budget';
        }
        
        if ($this->progress_percentage >= $this->alert_threshold) {
            return 'near_limit';
        }
        
        if ($this->progress_percentage >= 100) {
            return 'reached';
        }
        
        return 'in_progress';
    }

    /**
     * Mettre à jour le montant actuel
     */
    public function updateCurrentAmount(): void
    {
        if (!$this->category_id) {
            $this->current_amount = 0;
            $this->save();
            return;
        }

        $total = Transaction::where('category_id', $this->category_id)
            ->where('type', 'expense')
            ->whereBetween('date_transaction', [$this->start_date, $this->end_date ?? now()])
            ->sum('montant');

        $this->current_amount = $total ?? 0;
        $this->save();
    }

    /**
     * Vérifier et envoyer des alertes si nécessaire
     */
    public function checkAndSendAlerts(): bool
    {
        if (!$this->has_alert || !$this->is_active) {
            return false;
        }

        $this->updateCurrentAmount();
        
        $shouldAlert = false;
        $message = '';

        if ($this->is_over_budget) {
            $message = "Budget '{$this->name}' dépassé! Vous avez dépensé {$this->current_amount} FCFA sur {$this->amount} FCFA.";
            $shouldAlert = true;
        } elseif ($this->progress_percentage >= $this->alert_threshold) {
            $message = "Budget '{$this->name}' approche de la limite! {$this->progress_percentage}% utilisé ({$this->current_amount} FCFA / {$this->amount} FCFA).";
            $shouldAlert = true;
        }

        if ($shouldAlert && $message) {
            // Ici vous pouvez envoyer une notification
            // Par exemple: Notification::sendBudgetAlert($this->user, $message);
            
            return true;
        }

        return false;
    }

    /**
     * Scope pour les budgets actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les budgets d'un utilisateur
     */
    public function scopeForUser($query, $userId = null)
    {
        $userId = $userId ?: auth()->id();
        return $query->where('user_id', $userId);
    }

    /**
     * Scope pour les budgets en cours
     */
    public function scopeCurrent($query)
    {
        return $query->where('start_date', '<=', now())
            ->where(function($q) {
                $q->where('end_date', '>=', now())
                  ->orWhereNull('end_date');
            });
    }

    /**
     * Scope pour les budgets par période
     */
    public function scopeByPeriod($query, $period)
    {
        return $query->where('period', $period);
    }

    /**
     * Scope pour les budgets avec catégorie
     */
    public function scopeWithCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Vérifier si le budget est en cours
     */
    public function isCurrent(): bool
    {
        $now = now();
        
        if ($this->start_date > $now) {
            return false;
        }

        if ($this->end_date && $this->end_date < $now) {
            return false;
        }

        return true;
    }

    /**
     * Obtenir les statistiques du budget
     */
    public function getStatistics(): array
    {
        $this->updateCurrentAmount();

        return [
            'budget_amount' => $this->amount,
            'current_amount' => $this->current_amount,
            'remaining_amount' => $this->remaining_amount,
            'progress_percentage' => $this->progress_percentage,
            'days_remaining' => $this->days_remaining,
            'status' => $this->status,
            'is_over_budget' => $this->is_over_budget,
            'average_daily_spending' => $this->getAverageDailySpending(),
            'projected_end_amount' => $this->getProjectedEndAmount(),
        ];
    }

    /**
     * Dépense quotidienne moyenne
     */
    private function getAverageDailySpending(): float
    {
        $daysPassed = now()->diffInDays($this->start_date) + 1;
        
        if ($daysPassed <= 0) {
            return 0;
        }

        return round($this->current_amount / $daysPassed, 2);
    }

    /**
     * Montant projeté à la fin de la période
     */
    private function getProjectedEndAmount(): float
    {
        $averageDaily = $this->getAverageDailySpending();
        $totalDays = $this->end_date 
            ? $this->start_date->diffInDays($this->end_date) + 1
            : $this->start_date->diffInDays($this->start_date->endOfMonth()) + 1;

        return round($averageDaily * $totalDays, 2);
    }

    /**
     * Réinitialiser le budget pour une nouvelle période
     */
    public function resetForNewPeriod(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Déterminer la nouvelle période
        $newStartDate = $this->calculateNextPeriodStart();
        
        if (!$newStartDate) {
            return false;
        }

        // Créer une copie du budget pour la nouvelle période
        $newBudget = $this->replicate();
        $newBudget->current_amount = 0;
        $newBudget->start_date = $newStartDate;
        $newBudget->end_date = $this->calculatePeriodEnd($newStartDate);
        $newBudget->save();

        // Désactiver l'ancien budget
        $this->is_active = false;
        $this->save();

        return true;
    }

    /**
     * Calculer le début de la prochaine période
     */
    private function calculateNextPeriodStart(): ?Carbon
    {
        $currentEndDate = $this->end_date ?? $this->start_date->endOfMonth();
        
        if ($this->period === 'monthly') {
            return $currentEndDate->copy()->addDay();
        } elseif ($this->period === 'quarterly') {
            return $currentEndDate->copy()->addDay();
        } elseif ($this->period === 'yearly') {
            return $currentEndDate->copy()->addDay();
        } else {
            return null;
        }
    }

    /**
     * Calculer la fin de la période
     */
    private function calculatePeriodEnd(Carbon $startDate): ?Carbon
    {
        if ($this->period === 'monthly') {
            return $startDate->copy()->endOfMonth();
        } elseif ($this->period === 'quarterly') {
            return $startDate->copy()->addMonths(3)->subDay();
        } elseif ($this->period === 'yearly') {
            return $startDate->copy()->addYear()->subDay();
        } elseif ($this->period === 'custom') {
            return $this->end_date;
        } else {
            return $startDate->copy()->endOfMonth();
        }
    }
}