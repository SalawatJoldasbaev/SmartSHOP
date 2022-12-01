<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(App\Models\Category::class);
            $table->string('image', 2048)->nullable();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->json('cost_price');
            $table->json('min_price');
            $table->json('max_price');
            $table->json('whole_price');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
