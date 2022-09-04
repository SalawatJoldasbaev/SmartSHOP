<?php

use App\Models\Branch;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_history_baskets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class);
            $table->foreignIdFor(App\Models\Employee::class);
            $table->date('date');
            $table->text('description')->nullable();
            $table->json('additional')->nullable();
            $table->softDeletes();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('warehouse_history_baskets');
    }
};
