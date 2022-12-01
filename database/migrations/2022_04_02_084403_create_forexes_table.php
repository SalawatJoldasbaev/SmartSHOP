<?php

use App\Models\Forex;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forexes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(App\Models\Currency::class);
            $table->foreignId('to_currency_id');
            $table->double('rate');
            $table->timestamps();
        });

        Forex::create([
            'currency_id' => 2,
            'to_currency_id' => 1,
            'rate' => 10400,
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('forexes');
    }
};
