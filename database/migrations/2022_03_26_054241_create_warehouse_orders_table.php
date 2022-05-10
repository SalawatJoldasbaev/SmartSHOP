<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up():void
    {
        Schema::create('warehouse_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(App\Models\WarehouseBasket::class);
            $table->foreignIdFor(App\Models\Product::class);
            $table->foreignIdFor(App\Models\Unit::class);
            $table->double('count');
            $table->string('code');
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down():void
    {
        Schema::dropIfExists('warehouse_orders');
    }
};
