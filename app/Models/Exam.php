<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Exam
 *
 * @package App\Models
 *
 * @property int $id
 * @property int $class_semester_teacher_subject_id
 * @property string $name
 * @property int $max_points
 */
class Exam extends Model
{
    protected $casts = [
        'id' => 'integer',
        'class_semester_teacher_subject_id' => 'integer',
        'max_points' => 'integer'
    ];

    public function tasks() : HasMany {
        return $this->hasMany(ExamTask::class);
    }
    
    public function semesterTeacherSubject() : BelongsTo {
        return $this->belongsTo(SemesterTeacherSubject::class, 'class_semester_teacher_subject_id');
    }

    public function studentExams() : HasMany {
        return $this->hasMany(StudentExam::class);
    }
}