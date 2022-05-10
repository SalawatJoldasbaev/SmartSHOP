<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('phone')->unique();
            $table->enum('type', ['Y', 'J']);
            $table->integer('tin')->nullable();
            $table->double('balance')->default(0);
            $table->string('about')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        User::create([
            'full_name' => 'Unknown client',
            'phone' => '+998901231212',
            'type' => 'J',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
