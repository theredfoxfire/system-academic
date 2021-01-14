<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExamPoint extends Model
{
    //
    protected $table = 'exam_point';

    function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    function student()
    {
        return $this->belongsTo(Student::class);
    }
}
