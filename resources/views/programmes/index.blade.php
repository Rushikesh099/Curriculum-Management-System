<!DOCTYPE html>
<html>
<head>

<title>Programme List</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<h2 class="mb-4 text-center">Programme / Department List</h2>

@if(session('success'))
<div class="alert alert-success">
{{ session('success') }}
</div>
@endif


<div class="card shadow">

<div class="card-body">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>
<th>#</th>
<th>Programme Name</th>
<th>Programme Code</th>
<th style="width:250px;">Actions</th>
</tr>

</thead>

<tbody>

@foreach($programmes as $index => $programme)

<tr>

<td>{{ $index+1 }}</td>

<td>{{ $programme->programme_name }}</td>

<td>{{ $programme->programme_code }}</td>

<td>

<a href="/courses/{{ $programme->programme_code }}" class="btn btn-primary btn-sm">
See Courses
</a>

<form action="{{ route('programmes.destroy',$programme->programme_code) }}"
method="POST"
style="display:inline">

@csrf
@method('DELETE')

<button class="btn btn-danger btn-sm"
onclick="return confirm('Are you sure you want to delete this department? This will delete all related courses.')">
Delete
</button>

</form>

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>

</div>

</body>
</html>