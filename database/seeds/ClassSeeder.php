<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassSeeder extends Seeder
{
    public function run() {
        DB::table('classes')
            ->insert(
                [
                    ['name' => 'CTestA'],
                    ['name' => 'CTestB'],
                ]
            );
    }
}