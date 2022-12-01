<?php

use App\Models\Ingredient;
use App\Models\IngredientWarehouseBasket;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredient_warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(IngredientWarehouseBasket::class);
            $table->foreignIdFor(Ingredient::class);
            $table->double('count');
            $table->double('cost_price');
            $table->boolean('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredient_warehouses');
    }
};
