<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    public function run() {
        DB::table('students')
            ->insert(
                [
                    ['first_name' => 'STestA', 'last_name' => '1'],
                    ['first_name' => 'STestB', 'last_name' => '2'],
                    ['first_name' => 'STestC', 'last_name' => '3'],
                    ['first_name' => 'STestD', 'last_name' => '4'],
                    ['first_name' => 'STestE', 'last_name' => '5'],
                ]
            );
    }
}