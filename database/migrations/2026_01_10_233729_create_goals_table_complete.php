<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('goals')) {
            Schema::create('goals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
                $table->enum('type', ['savings', 'expense', 'income', 'custom']);
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('target_amount', 10, 2);
                $table->decimal('current_amount', 10, 2)->default(0);
                $table->boolean('completed')->default(false);
                $table->date('start_date');
                $table->date('deadline');
                $table->enum('frequency', ['daily', 'weekly', 'monthly', 'yearly', 'one_time']);
                $table->string('color', 7)->default('#6366f1');
                $table->string('icon', 50)->default('fa-bullseye');
                $table->json('notification_settings')->nullable();
                $table->timestamps();
                
                // Index pour améliorer les performances
                $table->index(['user_id', 'completed']);
                $table->index('deadline');
            });
        } else {
            // Si la table existe déjà, ajoutez les colonnes manquantes
            Schema::table('goals', function (Blueprint $table) {
                if (!Schema::hasColumn('goals', 'completed')) {
                    $table->boolean('completed')->default(false)->after('current_amount');
                }
                if (!Schema::hasColumn('goals', 'current_amount')) {
                    $table->decimal('current_amount', 10, 2)->default(0)->after('target_amount');
                }
                if (!Schema::hasColumn('goals', 'user_id')) {
                    $table->foreignId('user_id')->constrained()->onDelete('cascade')->after('id');
                }
                // Ajoutez d'autres colonnes manquantes si nécessaire...
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};