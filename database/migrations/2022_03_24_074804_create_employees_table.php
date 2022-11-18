<?php

use App\Models\Branch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class);
            $table->string('avatar')->nullable();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('password')->nullable();
            $table->string('pincode')->unique();
            $table->double('salary')->nullable();
            $table->double('flex')->nullable();
            $table->boolean('active')->default(true);
            $table->enum('role', ['saller', 'admin', 'ceo', 'warehouseManager']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
