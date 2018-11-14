<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Subject
 *
 * @package App\Models
 *
 * @property int id
 * @property string $name
 */
class Subject extends Model
{
    protected $casts = [
        'id' => 'integer'
    ];

    public function teachers() {
        return $this->belongsToMany(Teacher::class, 'teacher_subject');
    }
}