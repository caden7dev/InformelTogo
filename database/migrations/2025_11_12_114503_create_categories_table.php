<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['income', 'expense']);
            $table->string('color')->default('#6366f1');
            $table->string('icon')->default('tag');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'name', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
};