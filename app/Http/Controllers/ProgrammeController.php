<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scheme;
use App\Models\Course;

class ProgrammeController extends Controller
{

    public function index()
    {
        $programmes = Scheme::all();

        return view('programmes.index',compact('programmes'));
    }



    public function destroy($code)
    {

        // First delete courses of this programme
        Course::where('programme_code',$code)->delete();

        // Then delete programme
        Scheme::where('programme_code',$code)->delete();

        return redirect()->back()->with('success','Department deleted successfully');

    }

}