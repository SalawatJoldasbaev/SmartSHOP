<?php

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->default(0);
            $table->string('name');
            $table->double('min_percent');
            $table->double('max_percent');
            $table->double('whole_percent');
            $table->double('min_product')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Category::create([
        //     'name' => "Boshqa",
        //     'min_percent' => 1,
        //     'max_percent' => 1,
        //     'whole_percent' => 1,
        //     'min_product' => 1
        // ]);
    }


    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
