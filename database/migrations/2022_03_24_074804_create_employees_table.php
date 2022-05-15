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
            'name' => 'ceo',
            'phone' => '+998900957117',
            'password' => Hash::make(7117),
            'pincode' => md5(7117),
            'role' => 'ceo',
        ]);
        Employee::create([
            'name' => 'admin',
            'phone' => '+998905927117',
            'password' => Hash::make(7117),
            'pincode' => md5(2222),
            'role' => 'admin',
        ]);
        Employee::create([
            'name' => 'Saller',
            'phone' => '+998906503099',
            'password' => Hash::make(3333),
            'pincode' => md5(3333),
            'role' => 'saller',
        ]);
        Employee::create([
            'name' => 'TexnoPOS',
            'phone' => '+998900955882',
            'password' => Hash::make(5882),
            'pincode' => md5(5882),
            'role' => 'ceo',
        ]);

    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
