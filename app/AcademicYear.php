<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    //
    protected $table = 'academic_year';

    // function classRoom()
    // {
    //     return $this->belongsToMany(ClassRoom::class, 'teacher_class_to_subject');
    // }

    // function teachers()
    // {
    //     return $this->belongsToMany(Teacher::class, 'teacher_class_to_subject');
    // }
}
