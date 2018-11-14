<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SemesterTeacherSubject
 *
 * @package App\Models
 */
class SemesterTeacherSubject extends Model
{
    public function teachers() {
        return $this->belongsToMany(Teacher::class, 'teacher_subject', 'id','teacher_id', 'teacher_subject_id', 'id');
    }

    public function subjects() {
        return $this->belongsToMany(Subject::class, 'teacher_subject', 'id','subject_id', 'teacher_subject_id', 'id');
    }

    public function classSemester() {
        return $this->belongsTo(ClassSemester::class, 'class_semester_id');
    }
}