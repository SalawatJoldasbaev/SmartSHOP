<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->enum('type', ['product', ['basket']]);
            $table->json('additional');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};
