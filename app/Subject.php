<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    //
    protected $table = 'subject';

    function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_class_to_subject');
    }

    // function classRooms()
    // {
    //     return $this->belongsToMany(ClassRoom::class, 'teacher_class_to_subject');
    // }

    function exams()
    {
        return $this->belongsToMany(Exam::class, 'teacher_class_to_subject', 'subject_id', 'id', 'id', 'teacher_subject_id');
    }
}
