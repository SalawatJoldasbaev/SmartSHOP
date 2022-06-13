<?php

use App\Models\Ingredient;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up():void
    {
        Schema::create('ingredient_warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Ingredient::class);
            $table->double('count');
            $table->double('cost_price');
            $table->timestamp('ordered_at');
            $table->boolean('active');
            $table->timestamps();
        });
    }


    public function down():void
    {
        Schema::dropIfExists('ingredient_warehouses');
    }
};
