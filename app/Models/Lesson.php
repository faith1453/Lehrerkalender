<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Lesson
 *
 * @package App\Models
 *
 * @property SemesterTeacherSubject $semesterTeacherSubject
 *
 * @property int $id
 * @property int $class_semester_teacher_subject_id
 * @property string $topic
 * @property Carbon $start
 * @property Carbon $end
 */
class Lesson extends Model
{
    protected $casts = [
        'id' => 'integer',
        'class_semester_teacher_subject_id' => 'integer'
    ];

    protected $dates = [
        'start',
        'end'
    ];

    public function students() : BelongsToMany {
        return $this->belongsToMany(Student::class, 'lesson_student')
            ->withPivot(['grade']);
    }

    public function semesterTeacherSubject() : BelongsTo {
        return $this->belongsTo(SemesterTeacherSubject::class, 'class_semester_teacher_subject_id');
    }
}