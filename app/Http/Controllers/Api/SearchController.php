<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q');
        $results = [];

        // Search Users
        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->take(5)
            ->get();

        foreach ($users as $user) {
            $results[] = [
                'title' => $user->name,
                'description' => $user->email,
                'url' => route('users.show', $user),
                'icon' => $this->getUserIcon($user->role)
            ];
        }

        // Search Courses
        $courses = Course::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->take(5)
            ->get();

        foreach ($courses as $course) {
            $results[] = [
                'title' => $course->name,
                'description' => $course->description,
                'url' => route('courses.show', $course),
                'icon' => 'fa-book'
            ];
        }

        return response()->json($results);
    }

    private function getUserIcon($role)
    {
        return match($role) {
            'student' => 'fa-user-graduate',
            'teacher' => 'fa-chalkboard-teacher',
            'principal' => 'fa-user-tie',
            default => 'fa-user'
        };
    }
} 