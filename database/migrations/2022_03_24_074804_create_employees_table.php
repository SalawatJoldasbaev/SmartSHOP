<?php

use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('avatar')->nullable();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('password')->nullable();
            $table->string('pincode')->unique();
            $table->double('salary')->nullable();
            $table->double('flex')->nullable();
            $table->enum('role', ['saller', 'admin', 'ceo']);
            $table->timestamps();
            $table->softDeletes();
        });

        Employee::create([
            'name' => 'Salawat',
            'phone' => '+998906622939',
            'password' => Hash::make(1111),
            'pincode' => md5(1111),
            'role' => 'ceo',
        ]);
        Employee::create([
            'name' => 'Jaqsibay',
            'phone' => '+998913941113',
            'password' => Hash::make(2222),
            'pincode' => md5(2222),
            'role' => 'admin',
        ]);
        Employee::create([
            'name' => 'Saliq',
            'phone' => '+998906503099',
            'password' => Hash::make(3333),
            'pincode' => md5(3333),
            'role' => 'saller',
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
