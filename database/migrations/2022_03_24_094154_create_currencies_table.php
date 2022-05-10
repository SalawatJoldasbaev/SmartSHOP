<?php

use App\Models\Currency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->timestamps();
            $table->softDeletes();
        });
        Currency::create([
            'name' => 'Uzbek somi',
            'code' => 'UZS'
        ]);
        Currency::create([
            'name' => 'AQSH dollari',
            'code' => 'USD'
        ]);
    }


    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
