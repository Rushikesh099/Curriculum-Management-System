@extends('layouts.app')

@section('content')

<div class="container">

<h2 class="mb-4">
{{ $scheme->programme_name }} Courses
</h2>




<table class="table table-bordered table-striped">

<thead class="table-dark">

<tr>
<th>Course Code</th>
<th>Course Title</th>
<th>Credits</th>
<th>Action</th>
</tr>

</thead>

<tbody>

@foreach($courses as $course)

<tr>

<td>{{ $course->course_code }}</td>

<td>{{ $course->course_title }}</td>

<td>{{ $course->credits }}</td>

<td>

<a href="{{ route('courses.edit',$course->id) }}" class="btn btn-warning btn-sm">
Edit
</a>

<form action="{{ route('courses.delete',$course->id) }}" method="POST" style="display:inline">

@csrf
@method('DELETE')

<button class="btn btn-danger btn-sm"
onclick="return confirm('Are you sure you want to delete this course?')">

Delete

</button>

</form>

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

@endsection