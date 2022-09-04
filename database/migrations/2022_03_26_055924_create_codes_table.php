<?php

use App\Models\Branch;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('codes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class);
            $table->foreignIdFor(App\Models\WarehouseBasket::class)->nullable();
            $table->foreignIdFor(App\Models\WarehouseOrder::class)->nullable();
            $table->foreignIdFor(App\Models\Warehouse::class)->nullable();
            $table->foreignIdFor(App\Models\Product::class);
            $table->string('code')->unique();
            $table->json('cost_price')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('codes');
    }
};
