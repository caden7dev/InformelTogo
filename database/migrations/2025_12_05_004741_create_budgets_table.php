<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('budgets')) {
            Schema::create('budgets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
                $table->string('name');
                $table->text('description')->nullable();
                $table->enum('period', ['monthly', 'quarterly', 'yearly', 'custom'])->default('monthly');
                $table->decimal('amount', 15, 2);
                $table->decimal('current_amount', 15, 2)->default(0);
                $table->date('start_date');
                $table->date('end_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('has_alert')->default(true);
                $table->integer('alert_threshold')->default(80);
                $table->json('notification_settings')->nullable();
                $table->timestamps();
            });
        } else {
            // Si la table existe déjà, ajoutez seulement les colonnes manquantes
            Schema::table('budgets', function (Blueprint $table) {
                if (!Schema::hasColumn('budgets', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('end_date');
                }
                if (!Schema::hasColumn('budgets', 'has_alert')) {
                    $table->boolean('has_alert')->default(true)->after('is_active');
                }
                if (!Schema::hasColumn('budgets', 'alert_threshold')) {
                    $table->integer('alert_threshold')->default(80)->after('has_alert');
                }
                if (!Schema::hasColumn('budgets', 'notification_settings')) {
                    $table->json('notification_settings')->nullable()->after('alert_threshold');
                }
            });
        }
    }

    public function down()
    {
        // Ne pas supprimer la table si elle existe
        Schema::table('budgets', function (Blueprint $table) {
            $columns = ['is_active', 'has_alert', 'alert_threshold', 'notification_settings'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('budgets', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};