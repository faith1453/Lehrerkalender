<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassSemesterSeeder extends Seeder
{
    public function run() {
        DB::table('class_semesters')
            ->insert(
                [
                    ['class_id' => 1, 'class_teacher_id' => 1, 'start' => '2018-11-01', 'end' => '2018-11-30'],
                    ['class_id' => 2, 'class_teacher_id' => 1, 'start' => '2018-11-01', 'end' => '2018-11-30'],
                ]
            );
    }
}