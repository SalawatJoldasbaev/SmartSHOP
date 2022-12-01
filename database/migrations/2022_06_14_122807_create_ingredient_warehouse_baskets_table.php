<?php

use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredient_warehouse_baskets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class);
            $table->double('usd_rate');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredient_warehouse_baskets');
    }
};
