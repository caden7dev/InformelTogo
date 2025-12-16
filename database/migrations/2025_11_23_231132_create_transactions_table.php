<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['income', 'expense']);
            $table->decimal('montant', 10, 2);
            $table->string('description');
            $table->date('date_transaction');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'date_transaction']);
            $table->index(['type', 'date_transaction']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};