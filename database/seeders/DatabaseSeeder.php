<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Currency;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Branch::create([
            'name' => 'Main branch',
            'is_main' => true,
        ]);
        Currency::create([
            'name' => 'Uzbek somi',
            'code' => 'UZS',
        ]);
        Currency::create([
            'name' => 'AQSH dollari',
            'code' => 'USD',
        ]);

        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            EmployeeSeeder::class,
            UserSeeder::class,
        ]);
    }
}
