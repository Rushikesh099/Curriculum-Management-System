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
        // load the course early so we know if it's an audit item
        $course = Course::findOrFail($id);

        // ensure required/numeric inputs are sane
        $rules = [
            'course_code'  => 'required|string|max:255',
            'course_title' => 'required|string|max:255',
            'th'           => 'nullable|numeric|min:0',
            'tu'           => 'nullable|numeric|min:0',
            'pr'           => 'nullable|numeric|min:0',
            'total_hours'  => 'nullable|numeric|min:0',
            'marks'        => 'nullable|numeric|min:0',
        ];

        $messages = [];

        // credits validation only applies to non-audit courses
        if (!$course->is_audit) {
            $rules['credits'] = 'required|numeric|min:1';
            $messages = [
                'credits.required' => 'Credits field is required.',
                'credits.numeric'  => 'Credits must be a number.',
                'credits.min'      => 'Credits must be at least 1.',
            ];
        }

        $request->validate($rules, $messages);

        // continue using the already-loaded $course

    $levelId = $course->scheme_level_id;

    $level = SchemeLevel::find($levelId);

    // only perform the total-credit check for non-audit courses
    if (! $course->is_audit) {
        $currentCredits = Course::where('scheme_level_id',$levelId)
            ->where('id','!=',$id)
            ->sum('credits');

        $newTotal = $currentCredits + $request->credits;
        $remainingCredits = $level->total_credits - $currentCredits;

        if($newTotal !== $level->total_credits){

            return back()->withErrors([
                'credits'=>"Total credits of this level not matched, allowed credits for this course is exactly {$remainingCredits}"
            ]);

        }
    }

    // ------------------------------------------------------------------
    // total hours exact-match validation (new requirement)
    // ------------------------------------------------------------------
    $currentHours = Course::where('scheme_level_id',$levelId)
        ->where('id','!=',$id)
        ->sum('total_hours');

    $newTotalHours = $currentHours + $request->total_hours;
    $remainingHours = $level->total_hours - $currentHours;

    if ($newTotalHours !== $level->total_hours) {
        return back()->withErrors([
            'total_hours' => "Current hours assigned: {$currentHours} out of {$level->total_hours}. " .
                              "You may only add up to {$remainingHours} more hours in this course."
        ]);
    }

    // ------------------------------------------------------------------
    // total marks exact-match validation (new requirement)
    // ------------------------------------------------------------------
    $currentMarks = Course::where('scheme_level_id',$levelId)
        ->where('id','!=',$id)
        ->sum('marks');

    $newTotalMarks = $currentMarks + $request->marks;
    $remainingMarks = $level->marks - $currentMarks;

    if ($newTotalMarks !== $level->marks) {
        return back()->withErrors([
            'marks' => "Current marks assigned: {$currentMarks} out of {$level->marks}. " .
                        "You may only add up to {$remainingMarks} more marks in this course."
        ]);
    }

    // Basic fields
    $course->course_code = $request->course_code;
    $course->course_title = $request->course_title;
    $course->Abbr = $request->Abbr;
    // if the course is audit we don't trust user input for credits
    $course->credits = $course->is_audit ? 0 : $request->credits;

    // ensure numeric fields never end up null (database columns are not nullable)
    $course->th = $request->th ?? 0;
    $course->tu = $request->tu ?? 0;
    $course->pr = $request->pr ?? 0;
    $course->total_hours = $request->total_hours ?? 0;

    $course->theory_hours = $request->theory_hours ?? 0;
    $course->theory_marks = $request->theory_marks ?? 0;
    $course->test_marks = $request->test_marks ?? 0;
    $course->pr_marks = $request->pr_marks ?? 0;
    $course->or_marks = $request->or_marks ?? 0;
    $course->tw_marks = $request->tw_marks ?? 0;

    $course->marks = $request->marks ?? 0;


    /*
    |--------------------------------------------------------------------------
    | Course Type
    |--------------------------------------------------------------------------
    */

    // default to compulsory if not provided (shouldn't happen normally)
    $course->type = $request->type ?? 'compulsory';


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
