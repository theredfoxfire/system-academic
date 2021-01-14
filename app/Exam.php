<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    //
    protected $table = 'exam';

    function examType()
    {
        return $this->belongsTo(ExamType::class);
    }

    function subjects()
    {
        //return $this->belongsToMany(Exam::class, 'teacher_class_to_subject');
        return $this->belongsToMany(Subject::class, 'teacher_class_to_subject', 'id', 'subject_id', 'teacher_subject_id', 'id');
    }

    function examPoints()
    {
        return $this->hasMany(ExamPoint::class);
    }
}
