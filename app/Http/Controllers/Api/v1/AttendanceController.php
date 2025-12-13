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
            // 'permissions' => $user->getAllPermissions()->pluck('name')
        ]);
    }

    public function show($id)
    {
        $attendance = Attendance::findOrFail($id);
        return response()->json(['data' => $attendance]);
    }


    public function punchIn(Request $request)
    {
        $current = Carbon::now('Asia/Kolkata');

        // ✅ Allow only between 9 AM and 9 PM
        $startTime = Carbon::parse($current->toDateString().' 09:00:00', 'Asia/Kolkata');
        $endTime   = Carbon::parse($current->toDateString().' 21:00:00', 'Asia/Kolkata');

        if ($current->lt($startTime) || $current->gt($endTime)) {
            return response()->json(['message' => 'Punch In allowed only between 9 AM and 9 PM'], 400);
        }

        $alreadyMarked = Attendance::where('user_id', auth()->id())
            ->where('date', $current->toDateString())
            ->exists();

        if ($alreadyMarked) {
            return response()->json(['message' => 'Already punched In today'], 400);
        }

        $validated = $request->validate([
            'in_photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

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
        $current = Carbon::now('Asia/Kolkata');

        // ✅ Allow only between 9 AM and 9 PM
        $startTime = Carbon::parse($current->toDateString().' 09:00:00', 'Asia/Kolkata');
        $endTime   = Carbon::parse($current->toDateString().' 21:00:00', 'Asia/Kolkata');

        if ($current->lt($startTime) || $current->gt($endTime)) {
            return response()->json(['message' => 'Punch Out allowed only between 9 AM and 9 PM'], 400);
        }

        $attendance = Attendance::findOrFail($id);

        if ($attendance->time_out) {
            return response()->json(['message' => 'Already punched Out'], 400);
        }

        $validated = $request->validate([
            'out_photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $attendance->time_out = $current->format('H:i:s');

        if ($request->hasFile('out_photo')) {
            $path = $request->file('out_photo')->store('attendances/out_selfies', 'public');
            $attendance->out_photo_path = $path;
        }

        $start = Carbon::parse($attendance->time_in, 'Asia/Kolkata');
        $end   = Carbon::parse($attendance->time_out, 'Asia/Kolkata');

        $attendance->working_hours = $start->diffInHours($end);

        $attendance->save();

        return response()->json([
            'message'    => 'Punch Out saved successfully',
            'attendance' => $attendance
        ]);
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        // Update basic fields
        $attendance->date = $request->date;
        $attendance->time_in = $request->time_in;
        $attendance->time_out = $request->time_out;
        // $attendance->status = $request->status;

        // Replace In Photo if new uploaded
        if ($request->hasFile('in_photo')) {
            $path = $request->file('in_photo')->store('attendances/in_selfies', 'public');
            dd($path);
            $attendance->in_photo_path = $path;
        }

        // Replace Out Photo if new uploaded
        // if ($request->hasFile('out_photo')) {
        //     $path = $request->file('out_photo')->store('attendances/out_selfies', 'public');
        //     $attendance->out_photo_path = $path;
        // }

        if ($request->hasFile('out_photo')) {
            \Log::info('New out_photo uploaded: '.$request->file('out_photo')->getClientOriginalName());
            $path = $request->file('out_photo')->store('attendances', 'public');
            $attendance->out_photo_path = $path;
        }


        $attendance->save();

        return response()->json([
            'message' => 'Attendance updated successfully',
            'data' => $attendance
        ]);
    }


    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);

        // Optional: prevent delete if already punched out
        // if ($attendance->time_out) {
        //     return response()->json(['message' => 'Cannot delete completed attendance'], 400);
        // }

        $attendance->delete();

        return response()->json(['message' => 'Attendance deleted successfully']);
    }


}
