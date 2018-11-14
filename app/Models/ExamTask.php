<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ExamTask
 *
 * @package App\Models
 *
 * @property int $id
 * @property int $exam_id
 * @property string $name
 * @property int $max_points
 */
class ExamTask extends Model
{
    protected $casts = [
        'id' => 'integer',
        'exam_id' => 'integer',
        'max_points' => 'integer'
    ];

    public function exam() : BelongsTo {
        return $this->belongsTo(Exam::class);
    }
}