<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function classes() {
        return $this->belongsToMany(SchoolClass::class, 'class_student')
            ->withPivot(
                [
                    'guest_period_start',
                    'guest_period_end'
                ]
            );
    }
}