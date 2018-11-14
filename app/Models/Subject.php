<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function teachers() : BelongsToMany {
        return $this->belongsToMany(Teacher::class, 'teacher_subject');
    }
}