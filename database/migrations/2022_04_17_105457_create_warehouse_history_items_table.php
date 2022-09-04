<?php

use App\Models\Branch;
use App\Models\Product;
use App\Models\WarehouseHistoryBasket;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_history_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WarehouseHistoryBasket::class);
            $table->foreignIdFor(Product::class);
            $table->double('count');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('warehouse_history_items');
    }
};
