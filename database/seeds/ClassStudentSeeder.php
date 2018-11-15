<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassStudentSeeder extends Seeder
{
    public function run() {
        DB::table('class_student')
            ->insert(
                [
                    ['class_id' => 1, 'student_id' => 1],
                    ['class_id' => 1, 'student_id' => 2],
                    ['class_id' => 2, 'student_id' => 3],
                    ['class_id' => 2, 'student_id' => 4],
                ]
            );
        DB::table('class_student')
            ->insert(
                [
                    ['class_id' => 2, 'student_id' => 5, 'guest_period_start' => '2018-11-07', 'guest_period_end' => '2018-11-14'],
                ]
            );
    }
}