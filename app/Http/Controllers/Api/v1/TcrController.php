<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tcr;
use Illuminate\Http\Request;

class TcrController extends Controller
{
    // List all TCRs (Admin view)
    public function index()
    {
        $tcrs = Tcr::with('user')->get()->map(function ($tcr) {
            return [
                'id'         => $tcr->id,
                'tcr_no'     => $tcr->tcr_no,
                'user_id'    => $tcr->user->user_id, // physical employee ID
                'status'     => $tcr->status,
                'sr_no'      => $tcr->sr_no,
                'payment_term' => $tcr->payment_term,
                'amount'     => $tcr->amount, // 👈 include amount in response
                'tcr_photo'  => $tcr->tcr_photo,
                'payment_screenshot' => $tcr->payment_screenshot,
            ];
        });

        return response()->json($tcrs);
    }

    // Admin bulk assigns TCR range
    public function bulkAssign(Request $request)
    {
        if (!auth()->user()->can('tcr-assign')) {
            return response()->json(['error'=>'Unauthorized'],403);
        }

        $validated = $request->validate([
            'first_tcr_no' => 'required|integer|min:1',
            'last_tcr_no'  => 'required|integer|gte:first_tcr_no',
            'user_id'      => 'required|exists:users,id',
        ]);

        $records = [];
        $duplicates = [];

        for ($tcrNo = $validated['first_tcr_no']; $tcrNo <= $validated['last_tcr_no']; $tcrNo++) {
            if (Tcr::where('tcr_no', $tcrNo)->exists()) {
                $duplicates[] = $tcrNo;
                continue;
            }

            $records[] = [
                'tcr_no' => $tcrNo,
                'user_id' => $validated['user_id'],
                'status' => 'assigned',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($records)) {
            Tcr::insert($records);
        }

        return response()->json([
            'message' => "TCRs {$validated['first_tcr_no']} to {$validated['last_tcr_no']} processed",
            'user_id' => $validated['user_id'],
            'inserted_count' => count($records),
            'duplicates' => $duplicates
        ]);
    }

    // Employee sees only their assigned TCRs
    public function assigned()
    {
        if (!auth()->user()->can('tcr-use')) {
            return response()->json(['error'=>'Unauthorized'],403);
        }

        return response()->json(
            Tcr::where('user_id', auth()->id())
               ->where('status','assigned')
               ->get()
        );
    }

    public function useTcr(Request $request, $id)
    {
        if (!auth()->user()->can('tcr-use')) {
            return response()->json(['error'=>'Unauthorized'],403);
        }

        // ✅ Verify TCR assigned to this employee
        $tcr = Tcr::where('id',$id)
                ->where('user_id',auth()->id())
                ->where('status','assigned')
                ->firstOrFail();

        $validated = $request->validate([
            'sr_no'             => 'required|string',
            'payment_term'      => 'required|in:case,online',
            'amount'            => 'required|numeric|min:0',
            'tcr_photo'         => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'payment_screenshot'=> 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        $srNo = $validated['sr_no'];
        $timestamp = now()->format('d_m_Y_H_i_s');
        $folder = "TCR/{$srNo}";

        // ✅ Save TCR photo
        if ($request->hasFile('tcr_photo')) {
            $filename = "SR_{$srNo}"."TCR_{$tcr->tcr_no}.".$request->file('tcr_photo')->getClientOriginalExtension();
            $path = $request->file('tcr_photo')->storeAs($folder, $filename, 'public');
            $validated['tcr_photo'] = $path;
        }

        // ✅ Save payment screenshot (if online)
        if ($request->hasFile('payment_screenshot')) {
            $filename = "payment_screenshot_{$timestamp}.".$request->file('payment_screenshot')->getClientOriginalExtension();
            $path = $request->file('payment_screenshot')->storeAs($folder, $filename, 'public');
            $validated['payment_screenshot'] = $path;
        }

        // ✅ Update TCR record
        $tcr->update([
            'sr_no'             => $validated['sr_no'],
            'payment_term'      => $validated['payment_term'],
            'amount'            => $validated['amount'],
            'tcr_photo'         => $validated['tcr_photo'] ?? null,
            'payment_screenshot'=> $validated['payment_screenshot'] ?? null,
            'status'            => 'used',
        ]);

        return response()->json([
            'message'=>'TCR submitted successfully, pending admin verification',
            'tcr'    => $tcr
        ],200);
    }


    // Permission-wise verification
    public function verify(Request $request, $id)
    {
        $user = auth()->user();
        $tcr  = Tcr::findOrFail($id);

        $validated = $request->validate([
            'action' => 'required|in:verified,rejected',
        ]);

        if ($user->can('tcr-verify')) {
            $tcr->update(['status' => $validated['action']]);
        } elseif ($user->can('tcr-verify-case')) {
            if ($tcr->payment_term !== 'case') {
                return response()->json(['error' => 'Unauthorized: You can only verify CASE payments'],403);
            }
            $tcr->update(['status' => $validated['action']]);
        } elseif ($user->can('tcr-verify-online')) {
            if ($tcr->payment_term !== 'online') {
                return response()->json(['error' => 'Unauthorized: You can only verify ONLINE payments'],403);
            }
            $tcr->update(['status' => $validated['action']]);
        } else {
            return response()->json(['error'=>'Unauthorized: No verification permission'],403);
        }

        return response()->json(['message'=>"TCR {$validated['action']} successfully"]);
    }

    // Delete TCR
    public function destroy($id)
    {
        if (!auth()->user()->can('tcr-delete')) {
            return response()->json(['error'=>'Unauthorized'],403);
        }

        Tcr::findOrFail($id)->delete();
        return response()->json(['message'=>'Deleted successfully']);
    }
}
