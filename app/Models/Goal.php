<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'type',
        'name',
        'description',
        'target_amount',
        'current_amount',
        'start_date',
        'deadline',
        'frequency',
        'color',
        'icon',
        'completed',
        'notification_settings'
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'start_date' => 'date',
        'deadline' => 'date',
        'completed' => 'boolean',
        'notification_settings' => 'array'
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
     * Calculer la progression
     */
    public function getProgressAttribute(): float
    {
        if ($this->target_amount == 0) {
            return 0;
        }
        
        return min(100, ($this->current_amount / $this->target_amount) * 100);
    }

    /**
     * Montant restant
     */
    public function getRemainingAttribute(): float
    {
        return max(0, $this->target_amount - $this->current_amount);
    }

    /**
     * Jours restants
     */
    public function getDaysRemainingAttribute(): int
    {
        return max(0, now()->diffInDays($this->deadline, false));
    }
}