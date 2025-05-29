<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getStats()
    {
        return response()->json([
            'students' => User::where('role', 'student')->count(),
            'teachers' => User::where('role', 'teacher')->count(),
            'principals' => User::where('role', 'principal')->count(),
            'courses' => Course::count(),
            'sessions' => Course::where('status', 'active')->count()
        ]);
    }

    public function getCourseStats(Request $request)
    {
        $range = $request->get('range', 'month');
        $now = Carbon::now();

        switch ($range) {
            case 'week':
                $startDate = $now->copy()->startOfWeek();
                $endDate = $now->copy()->endOfWeek();
                $format = 'D';
                break;
            case 'year':
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                $format = 'M';
                break;
            default: // month
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                $format = 'd';
                break;
        }

        $dates = [];
        $enrollments = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dates[] = $currentDate->format($format);
            $enrollments[] = Course::whereDate('created_at', $currentDate)->count();
            $currentDate->addDay();
        }

        return response()->json([
            'dates' => $dates,
            'enrollments' => $enrollments
        ]);
    }
} 