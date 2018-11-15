<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

/**
 * Class Teacher
 *
 * @package App\Models
 *
 * @property Collection|Subject[] $subjects
 * @property Collection|ClassSemester[] $mainClassSemesters
 * @property Collection|SemesterTeacherSubject[] $semesterTeacherSubjects
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property bool $is_admin
 */
class Teacher extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable;
    use Authorizable;

    protected $casts = [
        'id' => 'integer',
        'is_admin' => 'boolean'
    ];

    protected $fillable = [
        'username',
        'password',
        'email',
        'is_admin'
    ];

    protected $hidden = [
        'password'
    ];

    public function subjects() : BelongsToMany {
        return $this->belongsToMany(Subject::class, 'teacher_subject');
    }

    public function mainClassSemesters() : HasMany {
        return $this->hasMany(ClassSemester::class, 'class_teacher_id');
    }

    public function semesterTeacherSubjects() : BelongsToMany {
        return $this->belongsToMany(SemesterTeacherSubject::class, 'teacher_subject', 'teacher_id','id', 'id', 'teacher_subject_id');
    }
}
