<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeUserIdNullableInCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::table('categories', function (Blueprint $table) {
        $table->foreignId('user_id')->nullable()->change();
    });
}

public function down()
{
    Schema::table('categories', function (Blueprint $table) {
        $table->foreignId('user_id')->nullable(false)->change();
    });
}
}
