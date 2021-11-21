<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    //
    protected $table = 'class_room';

    function students()
    {
        return $this->hasMany(Student::class);
    }
    function classSubjects()
    {
        return $this->hasMany(TeacherClassToSubject::class, 'class_room_id');
    }

    function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_class_to_subject');
    }

    function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_class_to_subject');
    }

    function academic_years()
    {
        return $this->belongsToMany(AcademicYear::class, 'teacher_class_to_subject');
    }

    // function exams()
    // {
    //     return $this->belongsToMany(Exam::class, 'teacher_class_to_subject', "class_room_id", "id", null, "teacher_subject_id");
    // }
}
