<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class SemesterTeacherSubject
 *
 * @package App\Models
 *
 * @property Collection|Teacher[] $teachers
 * @property Collection|Subject[] $subjects
 * @property ClassSemester $classSemester
 * @property Collection|Exam[] $exams
 * @property Collection|Lesson[] $lessons
 */
class SemesterTeacherSubject extends Model
{
    protected $table = 'class_semester_teacher_subjects';

    public function teachers() : BelongsToMany {
        return $this->belongsToMany(Teacher::class, 'teacher_subject', 'id','teacher_id', 'teacher_subject_id', 'id');
    }

    public function subjects() : BelongsToMany {
        return $this->belongsToMany(Subject::class, 'teacher_subject', 'id','subject_id', 'teacher_subject_id', 'id');
    }

    public function classSemester() : BelongsTo {
        return $this->belongsTo(ClassSemester::class, 'class_semester_id');
    }
    
    public function exams() : HasMany {
        return $this->hasMany(Exam::class, 'class_semester_teacher_subject_id');
    }

    public function lessons() : HasMany {
        return $this->hasMany(Lesson::class, 'class_semester_teacher_subject_id');
    }
}