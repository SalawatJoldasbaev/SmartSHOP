<?php

use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up():void
    {
        Schema::create('warehouse_baskets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class);
            $table->date('date');
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down():void
    {
        Schema::dropIfExists('warehouse_baskets');
    }
};
