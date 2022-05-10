<?php

use App\Models\WarehouseBasket;
use App\Models\WarehouseHistoryBasket;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WarehouseHistoryBasket::class);
            $table->foreignIdFor(\App\Models\Employee::class);
            $table->text('description')->nullable();
            $table->enum('type', ['defect', 'gift', 'return']);
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('warehouse_histories');
    }
};
