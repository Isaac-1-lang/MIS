<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:principal,teacher')->except(['index', 'show']);
    }

    public function index()
    {
        if (auth()->user()->isPrincipal()) {
            $courses = Course::with('teacher')->latest()->get();
        } elseif (auth()->user()->isTeacher()) {
            $courses = auth()->user()->courses;
        } else {
            $courses = auth()->user()->courses;
        }

        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        return view('courses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $course = Course::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'teacher_id' => auth()->user()->isTeacher() ? auth()->id() : $request->teacher_id,
        ]);

        return redirect()->route('courses.index')
            ->with('success', 'Course created successfully.');
    }

    public function show(Course $course)
    {
        return view('courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $course->update($validated);

        return redirect()->route('courses.index')
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('courses.index')
            ->with('success', 'Course deleted successfully.');
    }
} 