<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    public function run() {
        DB::table('subjects')
            ->insert(
                [
                    ['name' => 'SubjectA'],
                    ['name' => 'SubjectB'],
                    ['name' => 'SubjectC'],
                ]
            );
    }
}