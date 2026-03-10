@extends('layouts.app')

@section('content')




<div class="container">

<h3>Edit Course</h3>

<form action="{{ route('courses.update',$course->id) }}" method="POST">

@csrf

<div class="row">

<div class="col-md-4">
<label>Course Code</label>
<input type="text" name="course_code" class="form-control"
value="{{ $course->course_code }}">
</div>

<div class="col-md-4">
<label>Course Title</label>
<input type="text" name="course_title" class="form-control"
value="{{ $course->course_title }}">
</div>

<div class="col-md-2">
<label>Abbreviation</label>
<input type="text" name="Abbr" class="form-control"
value="{{ $course->Abbr }}">
</div>

<div class="mb-3">
<label>Course Type</label>
<select name="type" id="type">

<option value="compulsory" {{ $course->type == 'compulsory' ? 'selected' : '' }}>
Compulsory
</option>

<option value="elective" {{ $course->type == 'elective' ? 'selected' : '' }}>
Elective
</option>

</select>


<div id="electiveGroupDiv">

<label>Elective Group</label>

<select name="elective_group">

<option value="1" {{ $course->elective_group == 1 ? 'selected' : '' }}>Elective 1</option>
<option value="2" {{ $course->elective_group == 2 ? 'selected' : '' }}>Elective 2</option>
<option value="3" {{ $course->elective_group == 3 ? 'selected' : '' }}>Elective 3</option>
<option value="4" {{ $course->elective_group == 4 ? 'selected' : '' }}>Elective 4</option>

</select>

</div>

<div class="form-check mt-2">

<input type="hidden" name="is_award" value="0">

<input type="checkbox"
name="is_award"
value="1"
class="form-check-input"
{{ $course->is_award ? 'checked' : '' }}>

<label class="form-check-label">
Include in Award Class Calculation
</label>

</div>

<div class="col-md-2">
<label>Credits</label>
<input type="number" name="credits" class="form-control"
value="{{ $course->credits }}">
</div>

</div>

<hr>

<h5>Teaching Scheme</h5>

<div class="row">

<div class="col-md-2">
<label>TH</label>
<input type="number" name="th" class="form-control th-input"
value="{{ $course->th }}">
</div>

<div class="col-md-2">
<label>TU</label>
<input type="number" name="tu" class="form-control tu-input"
value="{{ $course->tu }}">  
</div>

<div class="col-md-2">
<label>PR</label>
<input type="number" name="pr" class="form-control pr-input"
value="{{ $course->pr }}">
</div>

<div class="col-md-3">
<label>Total Hours</label>
<input type="number" name="total_hours" class="form-control total-hours-input" readonly
value="{{ $course->total_hours }}">
</div>

</div>

<hr>

<h5>Examination Scheme</h5>

<div class="row">

<div class="col-md-2">
<label>Theory Hours</label>
<input type="number" name="theory_hours" class="form-control"
value="{{ $course->theory_hours }}">
</div>

<div class="col-md-2">
<label>Theory Marks</label>
<input type="number" name="theory_marks" class="form-control theory-marks"
value="{{ $course->theory_marks }}">
</div>

<div class="col-md-2">
<label>Test Marks</label>
<input type="number" name="test_marks" class="form-control test-marks"
value="{{ $course->test_marks }}">
</div>

<div class="col-md-2">
<label>PR Marks</label>
<input type="number" name="pr_marks" class="form-control pr-marks"
value="{{ $course->pr_marks }}">
</div>

<div class="col-md-2">
<label>OR Marks</label>
<input type="number" name="or_marks" class="form-control or-marks"
value="{{ $course->or_marks }}">
</div>

<div class="col-md-2">
<label>TW Marks</label>
<input type="number" name="tw_marks" class="form-control tw-marks"
value="{{ $course->tw_marks }}">
</div>

</div>

<div class="row mt-3">

<div class="col-md-3">
<label>Exam Total</label>
<input type="number" name="marks" class="form-control exam-total-input" readonly
value="{{ $course->marks }}">
</div>

</div>

<br>

<button class="btn btn-success">
Update Course
</button>

<a href="{{ url()->previous() }}" class="btn btn-secondary">
Back
</a>

</form>

</div>

@endsection
<script>

document.addEventListener("DOMContentLoaded", function(){

    const typeSelect = document.getElementById("type");
    const electiveDiv = document.getElementById("electiveGroupDiv");

    function toggleElective(){

        if(typeSelect.value === "elective"){
            electiveDiv.style.display = "block";
        }else{
            electiveDiv.style.display = "none";
        }

    }

    // Page load check
    toggleElective();

    // On change
    typeSelect.addEventListener("change", toggleElective);

    // ----------------------------------------------------------------
    // live computation helpers
    // ----------------------------------------------------------------
    function safeNumber(value) {
        return parseFloat(value) || 0;
    }

    const thInput = document.querySelector('input[name="th"]');
    const tuInput = document.querySelector('input[name="tu"]');
    const prInput = document.querySelector('input[name="pr"]');
    const totalHoursInput = document.querySelector('input[name="total_hours"]');

    const theoryInput = document.querySelector('input[name="theory_marks"]');
    const testInput = document.querySelector('input[name="test_marks"]');
    const prMarksInput = document.querySelector('input[name="pr_marks"]');
    const orMarksInput = document.querySelector('input[name="or_marks"]');
    const twMarksInput = document.querySelector('input[name="tw_marks"]');
    const examTotalInput = document.querySelector('input[name="marks"]');

    function updateHours() {
        if (!thInput || !tuInput || !prInput || !totalHoursInput) return;
        totalHoursInput.value = safeNumber(thInput.value)
            + safeNumber(tuInput.value)
            + safeNumber(prInput.value);
    }

    function updateMarks() {
        if (!theoryInput || !testInput || !prMarksInput || !orMarksInput || !twMarksInput || !examTotalInput) return;
        examTotalInput.value = safeNumber(theoryInput.value)
            + safeNumber(testInput.value)
            + safeNumber(prMarksInput.value)
            + safeNumber(orMarksInput.value)
            + safeNumber(twMarksInput.value);
    }

    [thInput, tuInput, prInput].forEach(el => el && el.addEventListener('input', updateHours));
    [theoryInput, testInput, prMarksInput, orMarksInput, twMarksInput].forEach(el => el && el.addEventListener('input', updateMarks));

    // initialize on load
    updateHours();
    updateMarks();

});

</script>