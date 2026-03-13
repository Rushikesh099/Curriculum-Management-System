@extends('layouts.app')

@section('content')
    <div class="container booklet-container">

        <h2 class="mb-4">
            {{ $scheme->programme_name }} Courses
        </h2>

        @php
            function showValue($value)
            {
                return $value === null || $value === '' || $value === 0 ? '--' : $value;
            }
        @endphp

        @foreach ($levels as $schemeLevel)
            <div class="level-heading">
                {{ $schemeLevel->level_name }}
            </div>

            @php
                $levelCourses = $courses[$schemeLevel->id] ?? collect();
            @endphp

            @if ($levelCourses->count() > 0)
                <table class="booklet-table">

                    <thead>

                        <tr>
                            <th rowspan="3">Sr.<br>No.</th>
                            <th rowspan="3">Course<br>Code</th>
                            <th rowspan="3">Course Title</th>
                            <th rowspan="3">Course<br>Abbr.</th>

                            <th colspan="5">TEACHING SCHEME</th>

                            <th colspan="7">EXAMINATION SCHEME</th>

                            <th rowspan="3">Actions</th>
                        </tr>

                        <tr>
                            <th colspan="4">Hours per Week</th>
                            <th rowspan="2">Total<br>Credits</th>

                            <th colspan="2">Theory<br>Paper</th>
                            <th rowspan="2">Test</th>
                            <th rowspan="2">PR</th>
                            <th rowspan="2">OR</th>
                            <th rowspan="2">TW</th>
                            <th rowspan="2">Total</th>
                        </tr>

                        <tr>
                            <th>TH</th>
                            <th>TU</th>
                            <th>PR</th>
                            <th>Total<br>Hours</th>

                            <th>Hrs</th>
                            <th>Marks</th>
                        </tr>

                    </thead>

                    <tbody>

                        @foreach ($levelCourses as $i => $course)
                            <tr>

                                <td>{{ $i + 1 }}</td>

                                <td>{{ $course->course_code }}</td>

                                <td class="text-left">{{ $course->course_title }}</td>

                                <td>{{ $course->Abbr }}</td>

                                <td>{{ showValue($course->th) }}</td>
                                <td>{{ showValue($course->tu) }}</td>
                                <td>{{ showValue($course->pr) }}</td>

                                <td>{{ showValue($course->total_hours) }}</td>

                                <td>{{ showValue($course->credits) }}</td>

                                <td>{{ showValue($course->theory_hours) }}</td>
                                <td>{{ showValue($course->theory_marks) }}</td>
                                <td>{{ showValue($course->test_marks) }}</td>
                                <td>{{ showValue($course->pr_marks) }}</td>
                                <td>{{ showValue($course->or_marks) }}</td>
                                <td>{{ showValue($course->tw_marks) }}</td>

                                <td>{{ showValue($course->marks) }}</td>

                                <td>

                                    <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-warning btn-sm">
                                        Edit </a>

                                    <form action="{{ route('courses.delete', $course->id) }}" method="POST"
                                        style="display:inline">

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
            @else
                <div class="alert alert-warning">
                    No courses found for this level.
                </div>
            @endif
        @endforeach

    </div>
@endsection
