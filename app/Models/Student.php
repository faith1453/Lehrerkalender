<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Student
 *
 * @package App\Models
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 */
class Student extends Model
{
    protected $casts = [
        'id' => 'integer'
    ];

    protected $fillable = [
        'first_name',
        'last_name'
    ];

    public function classes() : BelongsToMany {
        return $this->belongsToMany(SchoolClass::class, 'class_student', 'student_id', 'class_id')
            ->withPivot(
                [
                    'guest_period_start',
                    'guest_period_end'
                ]
            );
    }

    public function lessons() : BelongsToMany {
        return $this->belongsToMany(Lesson::class, 'lesson_student')
            ->withPivot(['grade']);
    }

    public function exams() : HasMany {
        return $this->hasMany(StudentExam::class);
    }
}