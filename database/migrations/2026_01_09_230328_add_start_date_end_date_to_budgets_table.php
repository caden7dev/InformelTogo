<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStartDateEndDateToBudgetsTable extends Migration
{
    public function up()
    {
        Schema::table('budgets', function (Blueprint $table) {
            // Vérifier d'abord si les colonnes n'existent pas déjà
            if (!Schema::hasColumn('budgets', 'start_date')) {
                // Ne pas spécifier 'after()' ou utiliser une colonne qui existe
                // Option 1: Ajouter simplement sans spécifier la position
                $table->date('start_date')->nullable();
                
                // Option 2: Si vous voulez spécifier une position, utilisez une colonne qui existe
                // D'abord, vérifiez quelles colonnes existent
                // $table->date('start_date')->nullable()->after('spent'); // si 'spent' existe
                // OU
                // $table->date('start_date')->nullable()->after('amount'); // si 'amount' existe
            }
            
            if (!Schema::hasColumn('budgets', 'end_date')) {
                $table->date('end_date')->nullable(); // Sans after()
            }
        });
    }

    public function down()
    {
        Schema::table('budgets', function (Blueprint $table) {
            // Vérifier si les colonnes existent avant de les supprimer
            if (Schema::hasColumn('budgets', 'start_date')) {
                $table->dropColumn('start_date');
            }
            
            if (Schema::hasColumn('budgets', 'end_date')) {
                $table->dropColumn('end_date');
            }
        });
    }
}