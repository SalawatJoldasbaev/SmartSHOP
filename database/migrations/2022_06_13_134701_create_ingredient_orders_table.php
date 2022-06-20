<?php

use App\Models\IngredientBasket;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up():void
    {
        Schema::create('ingredient_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(IngredientBasket::class);
            $table->foreignIdFor(Product::class);
            $table->double('count');
            $table->json('ingredients');
            $table->timestamps();
        });
    }


    public function down():void
    {
        Schema::dropIfExists('ingredient_orders');
    }
};
