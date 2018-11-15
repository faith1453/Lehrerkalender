<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassSemesterTeacherSubjectSeeder extends Seeder
{
    public function run() {
        DB::table('class_semester_teacher_subjects')
            ->insert(
                [
                    ['teacher_subject_id' => 1, 'class_semester_id' => 1],
                    ['teacher_subject_id' => 2, 'class_semester_id' => 1],
                    ['teacher_subject_id' => 3, 'class_semester_id' => 1],
                    ['teacher_subject_id' => 2, 'class_semester_id' => 2],
                    ['teacher_subject_id' => 3, 'class_semester_id' => 2],
                ]
            );
    }
}