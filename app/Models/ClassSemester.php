<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ClassSemester
 *
 * @package App\Models
 *
 * @property int $id
 * @property int $class_id
 * @property int $class_teacher_id
 * @property Carbon $start
 * @property Carbon $end
 */
class ClassSemester extends Model
{
    protected $casts = [
        'id' => 'integer',
        'class_id' => 'integer',
        'class_teacher_id' => 'integer'
    ];

    protected $dates = [
        'start',
        'end'
    ];

    public function classTeacher() {
        return $this->belongsTo(Teacher::class, 'class_teacher_id');
    }

    public function schoolClass() {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}