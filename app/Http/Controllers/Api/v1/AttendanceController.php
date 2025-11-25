<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;


class AttendanceController extends Controller
{
    /**
     * ✅ Employee mark attendance
     */

public function store(Request $request)
{
    $alreadyMarked = Attendance::where('user_id', auth()->id())
        ->where('date', now()->toDateString())
        ->exists();

    if ($alreadyMarked) {
        return response()->json(['message' => 'Attendance already marked today'], 400);
    }

    $validated = $request->validate([
        'status'  => 'required|in:present,absent,leave',
        'remarks' => 'nullable|string',
        'photo'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $current = Carbon::now('Asia/Kolkata');

    $validated['user_id'] = auth()->id();
    $validated['date']    = $current->toDateString();
    $validated['time']    = $current->format('H:i:s');

    // ✅ Office start time fixed at 11:00 AM on same date
    $officeStart = Carbon::parse($validated['date'].' 11:00:00', 'Asia/Kolkata');
    $validated['is_late'] = $current->gt($officeStart);

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
            ->where('user_id', $user->id)   // 👈 fix here
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
