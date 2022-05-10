<?php

use App\Models\Unit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up():void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit');
            $table->timestamps();
            $table->softDeletes();
        });

        Unit::create([
            'name'=> 'Piece',
            'unit'=> 'pcs',
        ]);
        Unit::create([
            'name'=> 'Tone',
            'unit'=> 't',
        ]);
        Unit::create([
            'name'=> 'Kilogram',
            'unit'=> 'kg',
        ]);
        Unit::create([
            'name'=> 'Gram',
            'unit'=> 'gr',
        ]);
        Unit::create([
            'name'=> 'Meter',
            'unit'=> 'm',
        ]);
        Unit::create([
            'name'=> 'Centimeter',
            'unit'=> 'cm',
        ]);
        Unit::create([
            'name'=> 'Liter',
            'unit'=> 'l',
        ]);
    }


    public function down():void
    {
        Schema::dropIfExists('units');
    }
};
