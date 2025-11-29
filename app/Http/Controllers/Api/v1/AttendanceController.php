<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->can('attendance-view-all')) {
            $attendances = Attendance::with('user')->latest()->get();
        } else {
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

    public function punchIn(Request $request)
    {
        $alreadyMarked = Attendance::where('user_id', auth()->id())
            ->where('date', now()->toDateString())
            ->exists();

        if ($alreadyMarked) {
            return response()->json(['message' => 'Already punched In today'], 400);
        }

        $validated = $request->validate([
            'in_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $current = Carbon::now('Asia/Kolkata');

        $attendance = new Attendance();
        $attendance->user_id = auth()->id();
        $attendance->date = $current->toDateString();
        $attendance->time_in = $current->format('H:i:s');

        $officeStart = Carbon::parse($attendance->date, 'Asia/Kolkata')->setTime(11, 0, 0);


        $attendance->is_late = $current->gt($officeStart);

        if ($request->hasFile('in_photo')) {
            $path = $request->file('in_photo')->store('attendances/in_selfies', 'public');
            $attendance->in_photo_path = $path;
        }

        $attendance->save();

        return response()->json([
            'message'    => 'Punch In saved successfully',
            'attendance' => $attendance
        ], 201);
    }

    public function punchOut(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        if ($attendance->time_out) {
            return response()->json(['message' => 'Already punched Out'], 400);
        }

        $validated = $request->validate([
            'out_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $attendance->time_out = now('Asia/Kolkata')->format('H:i:s');

        if ($request->hasFile('out_photo')) {
            $path = $request->file('out_photo')->store('attendances/out_selfies', 'public');
            $attendance->out_photo_path = $path;
        }

        // ✅ Attach date to time_in and time_out
        $start = Carbon::parse($attendance->time_in, 'Asia/Kolkata');
        $end   = Carbon::parse($attendance->time_out, 'Asia/Kolkata');




        // ✅ Calculate working hours (can use minutes if needed)
        $attendance->working_hours = $start->diffInHours($end);

        $attendance->save();

        return response()->json([
            'message'    => 'Punch Out saved successfully',
            'attendance' => $attendance
        ]);
    }

}
