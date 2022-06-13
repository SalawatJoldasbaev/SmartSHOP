<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up():void
    {
        Schema::create('ingredient_product_times', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class);
            $table->double('preparation_day');
            $table->timestamps();
        });
    }


    public function down():void
    {
        Schema::dropIfExists('ingredient_product_times');
    }
};
