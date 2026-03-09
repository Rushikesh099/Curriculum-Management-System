<?php

use App\Http\Controllers\SchemeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;

use App\Http\Controllers\ProgrammeController;

Route::get('/', function () {
    return redirect('/scheme/create');
});

Route::get('/scheme/create', [SchemeController::class, 'create'])
    ->name('scheme.create');
Route::post('/scheme/store', [SchemeController::class, 'store'])->name('scheme.store');

Route::get('/scheme/{scheme}/levels/{level}/courses',
    [SchemeController::class, 'addCourses']
)->name('scheme.addCourses');

Route::post('/scheme/{scheme}/levels/{level}/courses',
    [SchemeController::class, 'storeCourses']
)->name('scheme.storeCourses');

Route::get('/scheme/{scheme}/summary', [SchemeController::class, 'summary'])
    ->name('scheme.summary');

Route::get('/scheme/{scheme}/page18', [SchemeController::class, 'page18'])
    ->name('scheme.page18');

Route::get('/courses/{programme_code}', [CourseController::class,'showCourses'])->name('courses.index');

Route::get('/courses/edit/{id}', [CourseController::class,'edit'])->name('courses.edit');

Route::post('/courses/update/{id}', [CourseController::class,'update'])->name('courses.update');

Route::delete('/courses/delete/{id}', [CourseController::class,'destroy'])->name('courses.delete');

Route::get('/programmes',[ProgrammeController::class,'index'])->name('programmes.index');

Route::delete('/programmes/{code}',[ProgrammeController::class,'destroy'])->name('programmes.destroy');