<?php

namespace App\Http\Controllers;

use App\Models\Goals;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GoalController extends Controller
{
    // Create goal
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $validated['user_id'] = auth()->id();

        $goal = Goals::create($validated);

        return response()->json([
            'message' => 'Goal created successfully',
            'goal' => $goal
        ]);
    }

    // Update status
    public function updateStatus(Request $request, $id)
    {
        $goal = Goals::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,complete'
        ]);

        // Set completion date if completed
        if ($validated['status'] === 'complete') {
            $goal->completion_date = Carbon::now();

            // Check if completed within end date -> award badge
            if (Carbon::now()->lte(Carbon::parse($goal->end_date))) {
                
            }
        }

        $goal->status = $validated['status'];
        $goal->save();

        return response()->json([
            'message' => 'Goal status updated',
            'goal' => $goal
        ]);
    }

    // List goals
    public function index()
    {
        $goals = Goals::where('user_id', auth()->id())->get();
        return response()->json($goals);
    }
}
