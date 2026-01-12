<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'region',
        'secteur',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        // ⛔️ NE PAS utiliser 'password' => 'hashed' dans les versions anciennes de Laravel
    ];

    /**
     * Hash the password when setting it (seule méthode pour les versions Laravel < 9)
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    /**
     * Relation avec les transactions de l'utilisateur
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Relation avec les catégories de l'utilisateur
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Crée les catégories par défaut pour un nouvel utilisateur
     */
    public static function createDefaultCategories($userId)
    {
        $defaultCategories = [
            // Recettes
            ['name' => 'Ventes produits', 'type' => 'income', 'color' => '#10B981'],
            ['name' => 'Services', 'type' => 'income', 'color' => '#059669'],
            // Dépenses
            ['name' => 'Achats stock', 'type' => 'expense', 'color' => '#EF4444'],
            ['name' => 'Loyer local', 'type' => 'expense', 'color' => '#DC2626'],
        ];

        foreach ($defaultCategories as $category) {
            \App\Models\Category::create([
                'name' => $category['name'],
                'type' => $category['type'],
                'color' => $category['color'],
                'user_id' => $userId,
            ]);
        }
    }

    /**
     * Vérifie si l'utilisateur est un administrateur
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Vérifie si l'utilisateur est un commerçant
     */
    public function isMerchant()
    {
        return $this->role === 'commercant';
    }

    /**
     * Scope pour les administrateurs
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope pour les commerçants
     */
    public function scopeMerchants($query)
    {
        return $query->where('role', 'commercant');
    }

    /**
     * Calcul du solde total de l'utilisateur
     */
    public function getBalanceAttribute()
    {
        $income = $this->transactions()->where('type', 'income')->sum('montant');
        $expense = $this->transactions()->where('type', 'expense')->sum('montant');
        return $income - $expense;
    }
    // Ajoute cette méthode dans la classe User
public function notifications()
{
    return $this->hasMany(Notification::class);
}

public function sentNotifications()
{
    return $this->hasMany(Notification::class, 'sender_id');
}

// Méthode pour obtenir les notifications non lues
public function unreadNotifications()
{
    return $this->notifications()->where('read', false);
}

public function goals()
{
    return $this->hasMany(Goal::class);
}
}