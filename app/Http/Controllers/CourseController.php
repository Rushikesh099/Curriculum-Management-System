<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Scheme;
use App\Models\SchemeLevel;

class CourseController extends Controller
{
    public function showCourses($programme_code)
{
        $scheme = Scheme::where('programme_code',$programme_code)->firstOrFail();

        $courses = Course::where('programme_code',$programme_code)->get();

        return view('courses.index',compact('courses','scheme'));
}
    public function edit($id)
    {
        $course = Course::findOrFail($id);

        return view('courses.edit',compact('course'));
    }

    public function update(Request $request,$id)
{

    $course = Course::findOrFail($id);

    $levelId = $course->scheme_level_id;

    $level = SchemeLevel::find($levelId);

    $currentCredits = Course::where('scheme_level_id',$levelId)
        ->where('id','!=',$id)
        ->sum('credits');

    $newTotal = $currentCredits + $request->credits;

    if($newTotal > $level->total_credits){

        return back()->withErrors([
            'credits'=>'Total credits of this level exceed allowed credits'
        ]);

    }

    // Basic fields
    $course->course_code = $request->course_code;
    $course->course_title = $request->course_title;
    $course->Abbr = $request->Abbr;
    $course->credits = $request->credits;

    $course->th = $request->th;
    $course->tu = $request->tu;
    $course->pr = $request->pr;
    $course->total_hours = $request->total_hours;

    $course->theory_hours = $request->theory_hours;
    $course->theory_marks = $request->theory_marks;
    $course->test_marks = $request->test_marks;
    $course->pr_marks = $request->pr_marks;
    $course->or_marks = $request->or_marks;
    $course->tw_marks = $request->tw_marks;

    $course->marks = $request->marks;


    /*
    |--------------------------------------------------------------------------
    | Course Type
    |--------------------------------------------------------------------------
    */

    $course->type = $request->type;


    /*
    |--------------------------------------------------------------------------
    | Elective Logic
    |--------------------------------------------------------------------------
    */

    if($request->type == 'elective'){
        $course->elective_group = (int) $request->elective_group;
    } 
    else{
        $course->elective_group = null;
    }


    /*
    |--------------------------------------------------------------------------
    | Award Class Checkbox
    |--------------------------------------------------------------------------
    */

    $course->is_award = $request->has('is_award') ? 1 : 0;


    $course->save();

    return redirect()->back()->with('success','Course Updated Successfully');

}

    public function destroy($id)
    {
        Course::findOrFail($id)->delete();

        return redirect()->back()->with('success','Course Deleted Successfully');
    }
}
