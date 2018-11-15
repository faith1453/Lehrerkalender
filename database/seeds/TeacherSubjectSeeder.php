<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeacherSubjectSeeder extends Seeder
{
    public function run() {
        DB::table('teacher_subject')
            ->insert(
                [
                    ['teacher_id' => 1, 'subject_id' => 1],
                    ['teacher_id' => 1, 'subject_id' => 2],
                    ['teacher_id' => 1, 'subject_id' => 3],
                ]
            );
    }
}