<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'programme_name',
        'programme_code',
        'year'
    ];

    public function courses()
    {
        return $this->hasMany(Course::class,'programme_code','programme_code');
    }
}