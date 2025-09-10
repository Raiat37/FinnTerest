<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SavingsApplication;
use Illuminate\Http\Request;


class SavingsAdminController extends Controller
{
    // GET /admin/savings 
    public function index(Request $request)
    {
        $status = $request->query('status'); 
        $q = SavingsApplication::with(['user:id,name,email', 'bank:id,name,website_url'])
            ->orderBy('status')
            ->orderByDesc('created_at');

        if ($status) $q->where('status', $status);

        return $q->get();
    }

    // POST /admin/savings/{id}/approve
    public function approve(Request $request, $id)
    {
        $admin = $request->user();
        if ($admin->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $app = SavingsApplication::with('bank')->findOrFail($id);

        if ($app->status !== 'pending') {
            return response()->json(['message' => 'Only pending applications can be approved.'], 422);
        }

        $app->update([
            'status'      => 'approved',
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Application approved.',
            'application' => $app->fresh()->load('bank')
        ]);
    }

    // POST /admin/savings/{id}/reject
    public function reject(Request $request, $id)
    {
        $admin = $request->user();
        if ($admin->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $app = SavingsApplication::findOrFail($id);

        if ($app->status !== 'pending') {
            return response()->json(['message' => 'Only pending applications can be rejected.'], 422);
        }

        $app->update([
            'status'      => 'rejected',
            'approved_by' => $admin->id,
            'approved_at' => null,
        ]);

        return response()->json([
            'message' => 'Application rejected.',
            'application' => $app->fresh()->load('bank')
        ]);
    }
}
