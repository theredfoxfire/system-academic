<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TeacherClassToSubject extends Pivot
{
    //
    // protected $table = 'teacher_class_to_subject';

    // public function teacher()
    // {
    //     return $this->belongsTo(Teacher::class, 'teacher_id');
    // }

    // public function exam()
    // {
    //     return $this->hasOne(Exams::class, 'teacher_subject_id');
    // }
}
