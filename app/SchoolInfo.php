<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolInfo extends Model
{
    //
    protected $table = 'school_info';

    // function classRooms()
    // {
    //     return $this->belongsToMany(ClassRoom::class, 'teacher_class_to_subject');
    // }
}
