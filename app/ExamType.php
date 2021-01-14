<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExamType extends Model
{
    //
    protected $table = 'exam_type';

    function exams()
    {
        return $this->hasMany(Exam::class);
    }
}
