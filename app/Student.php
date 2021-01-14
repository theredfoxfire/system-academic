<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    //
    protected $table = 'student';

    function examPoints()
    {
        return $this->hasMany(ExamPoint::class);
    }
}
