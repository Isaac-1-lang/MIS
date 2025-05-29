<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with('user')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($activity) {
                return [
                    'type' => $activity->type,
                    'description' => $activity->description,
                    'time' => $activity->created_at->diffForHumans()
                ];
            });

        return response()->json($activities);
    }

    public static function log($userId, $type, $description, $related = null)
    {
        $activity = new Activity([
            'user_id' => $userId,
            'type' => $type,
            'description' => $description
        ]);

        if ($related) {
            $activity->related()->associate($related);
        }

        $activity->save();

        // Broadcast the activity
        event(new \App\Events\NewActivity($activity));

        return $activity;
    }
} 