<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'scheme_id',
        'scheme_level_id',
        'programme_code',
        'course_code',
        'course_title',
        'Abbr',
        'year',
        'term',
        'th',
        'tu',
        'pr',
        'total_hours',
        'credits',
        'marks',
        'type',
        'is_audit',
        'is_award'
    ];
}