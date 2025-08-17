<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Allotment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CategorySpendingController extends Controller
{
    public function index()
    {
        $userId = auth()->id(); // current logged-in user

        // Fetch and group allotments by category (assuming "description" is the category name)
        $data = Allotment::select(
                'description as category',
                DB::raw('SUM(allotment) as spent'),
                DB::raw('MAX(end_date) as end_date')
            )
            ->where('user_id', $userId)
            ->groupBy('description')
            ->get();

        // Get total budget from the user table
        $userBudget = auth()->user()->budget ?? 0;

        // Format data for frontend
        $result = $data->map(function ($item) use ($userBudget) {
            $status = Carbon::now()->gt(Carbon::parse($item->end_date)) ? 'overdue' : 'active';

            return [
                'category' => $item->category ?? 'Uncategorized',
                'budget' => $userBudget,
                'spent' => (int) $item->spent,
                'status' => $status
            ];
        });

        return response()->json($result);
    }
}
