<h2>{{ $scheme->programme_name }} Courses</h2>

<table border="1">
<tr>
<th>Course Code</th>
<th>Course Title</th>   
<th>Credits</th>
</tr>

@foreach($courses as $course)

<tr>
<td>{{ $course->course_code }}</td>
<td>{{ $course->course_title }}</td>
<td>{{ $course->credits }}</td>
</tr>

@endforeach

</table>