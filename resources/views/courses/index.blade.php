@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Courses</h1>
        @if(auth()->user()->role === 'principal' || auth()->user()->role === 'teacher')
            <a href="{{ route('courses.create') }}" class="btn btn-primary">Create New Course</a>
        @endif
    </div>

    <div class="row">
        @foreach($courses as $course)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $course->name }}</h5>
                        <p class="card-text">{{ Str::limit($course->description, 100) }}</p>
                        <p class="card-text">
                            <small class="text-muted">
                                Teacher: {{ $course->teacher->name }}
                            </small>
                        </p>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('courses.show', $course) }}" class="btn btn-info btn-sm">View Details</a>
                            @if(auth()->user()->role === 'principal' || (auth()->user()->role === 'teacher' && $course->teacher_id === auth()->id()))
                                <div>
                                    <a href="{{ route('courses.edit', $course) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('courses.destroy', $course) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this course?')">Delete</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection 