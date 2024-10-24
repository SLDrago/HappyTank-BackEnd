<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Post;
use App\Models\ReportedContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReportedContentController extends Controller
{
    public function addReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content_type' => 'required|in:Advertisement,Comment,Review,User,Post',
            'content_id' => 'required|integer',
            'report_reason' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $existingReport = ReportedContent::where([
            'content_type' => $request->content_type,
            'content_id' => $request->content_id,
            'reporter_id' => Auth::id(),
        ])->first();

        if ($existingReport) {
            return response()->json(['message' => 'You have already reported this content'], 409);
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
            'status' => 'required|in:Pending,Resolved',
            'resolution_notes' => 'nullable|string',
            'advertisement_id' => 'nullable|integer',
            'post_id' => 'nullable|integer',
            'hide' => 'required|string',
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

        if ($request->hide !== 'false' && $request->advertisement_id != null) {
            $advertisement = Advertisement::findOrFail($request->advertisement_id);

            $advertisement->update([
                'status' => 0,
            ]);
        }

        if ($request->hide !== 'false' && $request->post_id != null) {
            $post = Post::findOrFail($request->post_id);

            $post->update([
                'status' => 0,
            ]);
        }

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

    public function showReportsByType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content_type' => 'required|in:Advertisement,Comment,Review,User,Post',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $reports = ReportedContent::where('content_type', $request->content_type)->get();

        return response()->json(['reports' => $reports], 200);
    }

    public function showReportedAdvertisements(Request $request)
    {
        $reports = ReportedContent::where('content_type', 'Advertisement')
            ->where('reported_content.status', 'pending')
            ->join('advertisements', 'reported_content.content_id', '=', 'advertisements.id')
            ->join('users', 'reported_content.reporter_id', '=', 'users.id')
            ->select(
                'reported_content.id as reportId',
                'advertisements.id as adId',
                'advertisements.title as adTitle',
                'users.name as reportedBy',
                'reported_content.report_reason as reportedReason',
                'reported_content.created_at as dateReported'
            )
            ->get();

        return response()->json($reports, 200);
    }

    public function showReportedPosts(Request $request)
    {
        $reports = ReportedContent::where('content_type', 'Post')
            ->where('reported_content.status', 'pending')
            ->join('posts', 'reported_content.content_id', '=', 'posts.id')
            ->join('users', 'reported_content.reporter_id', '=', 'users.id')
            ->select(
                'reported_content.id as reportId',
                'posts.content as postContent',
                'users.name as reportedBy',
                'reported_content.report_reason as reportedReason',
                'reported_content.created_at as dateReported'
            )
            ->get();

        return response()->json($reports, 200);
    }


    public function getReportedAdvertisementById(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $report = ReportedContent::where('reported_content.id', $request->id)
            ->join('advertisements', 'reported_content.content_id', '=', 'advertisements.id')
            ->select('reported_content.*', 'advertisements.*')
            ->first();

        if (!$report) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        return response()->json($report, 200);
    }

    public function getReportedPostsById(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $report = ReportedContent::where('reported_content.id', $request->id)
            ->join('posts', 'reported_content.content_id', '=', 'posts.id')
            ->select('reported_content.*', 'posts.*')
            ->first();

        if (!$report) {
            return response()->json(['message' => 'Report not found'], 404);
        }

        return response()->json($report, 200);
    }
}
