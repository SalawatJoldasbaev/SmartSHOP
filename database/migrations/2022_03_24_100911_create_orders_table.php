<?php

use App\Models\Branch;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class);
            $table->foreignIdFor(App\Models\Basket::class);
            $table->foreignIdFor(App\Models\User::class);
            $table->foreignIdFor(App\Models\Product::class);
            $table->foreignIdFor(\App\Models\Unit::class);
            $table->double('count');
            $table->double('price');
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
