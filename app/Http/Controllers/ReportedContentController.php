<?php

namespace App\Http\Controllers;

use App\Models\ReportedContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReportedContentController extends Controller
{
    public function addReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content_type' => 'required|in:Advertisement,Comment,Review,User',
            'content_id' => 'required|integer',
            'report_reason' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $report = ReportedContent::create([
            'content_type' => $request->content_type,
            'content_id' => $request->content_id,
            'reporter_id' => Auth::id(),
            'report_reason' => $request->report_reason,
        ]);

        return response()->json(['message' => 'Report created successfully', 'report' => $report], 201);
    }

    public function updateReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'report_id' => 'required|integer',
            'status' => 'required|in:Pending,Reviewed,Resolved',
            'resolution_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $report = ReportedContent::findOrFail($request->report_id);

        $report->update([
            'status' => $request->status,
            'review_date' => now(),
            'reviewer_id' => Auth::id(),
            'resolution_notes' => $request->resolution_notes,
        ]);

        return response()->json(['message' => 'Report updated successfully', 'report' => $report], 200);
    }

    public function destroyReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'report_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $report = ReportedContent::findOrFail($request->report_id);

        $report->delete();

        return response()->json(['message' => 'Report deleted successfully'], 200);
    }
}
