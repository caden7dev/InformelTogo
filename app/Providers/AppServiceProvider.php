<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema; // Ajoutez cette ligne

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 🔥 AJOUTEZ CETTE LIGNE POUR RÉSOUDRE L'ERREUR DE MIGRATION
        Schema::defaultStringLength(191);

        // Directive pour formater les montants en FCFA
        Blade::directive('currency', function ($expression) {
            return "<?php echo number_format($expression, 0, ',', ' ') . ' FCFA'; ?>";
        });

        // Directive pour formater les nombres seulement (sans FCFA)
        Blade::directive('currencyNumber', function ($expression) {
            return "<?php echo number_format($expression, 0, ',', ' '); ?>";
        });
    }
}