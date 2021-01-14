<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    //
    protected $table = 'teacher';

    function Exams()
    {
        //return $this->belongsToMany(Exam::class, 'teacher_class_to_subject');
        return $this->belongsToMany(Exam::class, 'teacher_class_to_subject', "teacher_id", "id", null, "teacher_subject_id");
    }
}
