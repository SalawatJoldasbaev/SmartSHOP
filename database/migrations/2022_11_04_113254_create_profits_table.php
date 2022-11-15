<?php

use App\Models\Branch;
use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profits', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignIdFor(Branch::class);
            $table->foreignIdFor(Category::class);
            $table->double('profit')->nullable();
            $table->double('sum')->nullable();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('profits');
    }
};
