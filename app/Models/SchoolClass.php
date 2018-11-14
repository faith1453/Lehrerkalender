<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SchoolClass
 *
 * @package App\Models
 *
 * @property int $id
 * @property string $name
 */
class SchoolClass extends Model
{
    protected $table = 'classes';

    protected $casts = [
        'id' => 'integer'
    ];

    public function students() {
        return $this->belongsToMany(Student::class, 'class_student')
            ->withPivot(
                [
                    'guest_period_start',
                    'guest_period_end'
                ]
            );
    }

    public function semesters() {
        return $this->hasMany(ClassSemester::class, 'class_id');
    }
}