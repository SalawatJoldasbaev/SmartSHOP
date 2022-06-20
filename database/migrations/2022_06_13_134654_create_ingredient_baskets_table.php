<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up():void
    {
        Schema::create('ingredient_baskets', function (Blueprint $table) {
            $table->id();
            $table->string('deadline');
            $table->boolean('active');
            $table->timestamps();
        });
    }


    public function down():void
    {
        Schema::dropIfExists('ingredient_baskets');
    }
};
