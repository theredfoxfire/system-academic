<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TeacherClassToSubject extends Pivot
{
    
    protected $table = 'teacher_class_to_subject';

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'teacher_subject_id');
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
}
