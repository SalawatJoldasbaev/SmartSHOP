<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Employee::create([
            'branch_id' => 1,
            'name' => 'ceo',
            'phone' => '+998900957117',
            'password' => Hash::make(7117),
            'pincode' => md5(7117),
            'role' => 'ceo',
        ]);
        Employee::create([
            'branch_id' => 1,
            'name' => 'admin',
            'phone' => '+998905927117',
            'password' => Hash::make(7117),
            'pincode' => md5(2222),
            'role' => 'admin',
        ]);
        Employee::create([
            'branch_id' => 1,
            'name' => 'Saller',
            'phone' => '+998906503099',
            'password' => Hash::make(3333),
            'pincode' => md5(3333),
            'role' => 'saller',
        ]);
        Employee::create([
            'branch_id' => 1,
            'name' => 'TexnoPOS',
            'phone' => '+998900955882',
            'password' => Hash::make(5882),
            'pincode' => md5(5882),
            'role' => 'ceo',
        ]);

    }
}
