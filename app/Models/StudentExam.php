<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class StudentExam
 *
 * @package App\Models
 *
 * @property int $id
 * @property int $student_id
 * @property int $exam_id
 */
class StudentExam extends Model
{
    protected $casts = [
        'id' => 'integer',
        'student_id' => 'integer',
        'exam_id' => 'integer'
    ];

    public function student() : BelongsTo {
        return $this->belongsTo(Student::class);
    }

    public function exam() : BelongsTo {
        return $this->belongsTo(Exam::class);
    }

    public function tasks() : BelongsToMany {
        return $this->belongsToMany(ExamTask::class, 'student_exam_task')
            ->withPivot(['points']);
    }
}