<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    /**
     * ✅ Employee mark attendance
     */
    public function store(Request $request)
    {
        // Prevent duplicate attendance for same day
        $alreadyMarked = Attendance::where('user_id', auth()->id())
            ->where('date', now()->toDateString())
            ->exists();

        if ($alreadyMarked) {
            return response()->json(['message' => 'Attendance already marked today'], 400);
        }

        // Validate request
        $validated = $request->validate([
            'status'  => 'required|in:present,absent,leave',
            'remarks' => 'nullable|string',
            'photo'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['date']    = now()->toDateString();

        // ✅ If photo uploaded, store it
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('attendances/selfies', 'public');
            $validated['photo_path'] = $path;
        }

        $attendance = Attendance::create($validated);

        return response()->json([
            'message'    => 'Attendance saved successfully',
            'attendance' => $attendance
        ], 201);
    }

    /**
     * ✅ Admin: list all attendances
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->can('attendance-view-all')) {
            // Admin → બધાનું attendance
            $attendances = Attendance::with('user')->latest()->get();
        } else {
            // Employee → ફક્ત પોતાનું attendance
            $attendances = Attendance::with('user')
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }

        return response()->json([
            'data'        => $attendances,
            'permissions' => $user->getAllPermissions()->pluck('name')
        ]);
    }

    /**
     * ✅ Admin: delete attendance
     */
    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return response()->json(['message' => 'Attendance deleted successfully']);
    }
}
