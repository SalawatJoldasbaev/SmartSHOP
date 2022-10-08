<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'branch_id' => 1,
            'full_name' => 'Unknown client',
            'phone' => '+998901231212',
            'type' => 'J',
        ]);

    }
}
