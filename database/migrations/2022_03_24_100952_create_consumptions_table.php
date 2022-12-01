<?php

use App\Models\Branch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class);
            $table->foreignIdFor(App\Models\Employee::class);
            $table->foreignIdFor(App\Models\ConsumptionCategory::class);
            $table->string('whom');
            $table->date('date');
            $table->double('price');
            $table->string('description')->nullable();
            $table->enum('type', ['income', 'consumption']);
            $table->enum('payment_type', ['card', 'cash']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumptions');
    }
};
