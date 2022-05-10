<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('baskets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(App\Models\User::class);
            $table->foreignIdFor(App\Models\Employee::class);
            $table->double('card')->nullable();
            $table->double('cash')->nullable();
            $table->json('debt')->nullable();
            $table->date('term')->nullable();
            $table->string('description')->nullable();
            $table->enum('status', ['finished', 'draft']);
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('baskets');
    }
};
