<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sender_id',
        'title',
        'message',
        'type',
        'read',
        'data',
        'icon'
    ];

    protected $casts = [
        'read' => 'boolean',
        'data' => 'array'
    ];

    // Relation avec l'utilisateur destinataire
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec l'expéditeur
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Méthode pour marquer comme lu
    public function markAsRead()
    {
        $this->update(['read' => true]);
        return $this;
    }

    // Méthode pour obtenir l'icône selon le type
    public function getIconAttribute($value)
    {
        if ($value) {
            return $value;
        }

        $icons = [
            'info' => 'fa-info-circle',
            'success' => 'fa-check-circle',
            'warning' => 'fa-exclamation-triangle',
            'error' => 'fa-times-circle'
        ];

        return $icons[$this->type] ?? 'fa-bell';
    }

    // Méthode pour obtenir la couleur selon le type
    public function getColorClass()
    {
        $colors = [
            'info' => 'bg-blue-100 text-blue-600',
            'success' => 'bg-green-100 text-green-600',
            'warning' => 'bg-yellow-100 text-yellow-600',
            'error' => 'bg-red-100 text-red-600'
        ];

        return $colors[$this->type] ?? 'bg-gray-100 text-gray-600';
    }

    // Méthode pour formater le temps écoulé
    public function getTimeAgoAttribute()
    {
        $now = now();
        $diff = $now->diffInSeconds($this->created_at);

        if ($diff < 60) {
            return 'À l\'instant';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return "Il y a {$minutes} min";
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return "Il y a {$hours} h";
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return "Il y a {$days} j";
        } else {
            return $this->created_at->format('d/m/Y');
        }
    }
}