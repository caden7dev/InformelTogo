<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Vérifier si les colonnes existent avant de les ajouter
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'commercant'])->default('commercant');
            }
            
            if (!Schema::hasColumn('users', 'region')) {
                $table->string('region')->nullable();
            }
            
            if (!Schema::hasColumn('users', 'secteur')) {
                $table->string('secteur')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'region', 'secteur']);
        });
    }
};