<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class ReviewController extends Controller
{
    public function index()
    {
        try {
            $reviews = Review::all();
            return response()->json($reviews);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch reviews'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addReview(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'review_text' => 'required|string|max:255',
                'rating' => 'required|integer|between:1,5',
                'advertisement_id' => 'required|exists:advertisements,id'
            ]);

            $user = Auth::user();
            $user_id = $user->id;
            $advertisement_id = $validatedData['advertisement_id'];

            // Check if the user has already reviewed this advertisement
            $existingReview = Review::where('user_id', $user_id)
                ->where('advertisement_id', $advertisement_id)
                ->first();

            if ($existingReview) {
                return response()->json(['error' => 'You have already added a review for this advertisement'], Response::HTTP_CONFLICT);
            }

            $review = Review::create([
                'title' => $validatedData['title'],
                'review_text' => $validatedData['review_text'],
                'rating' => $validatedData['rating'],
                'advertisement_id' => $advertisement_id,
                'user_id' => $user_id
            ]);

            return response()->json(['message' => 'Review added successfully', 'review' => $review], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create review'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function showReviewByID(Request $request)
    {
        try {
            $validatedData = $request->validate(['id' => 'required']);
            $id = $validatedData['id'];

            $review = Review::findOrFail($id);
            return response()->json($review);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Review not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch review'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateReview(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id' => 'required',
                'title' => 'sometimes|required|string|max:255',
                'review_text' => 'sometimes|required|string|max:255',
                'rating' => 'sometimes|required|integer|between:1,5'
            ]);

            $id = $validatedData['id'];

            $review = Review::findOrFail($id);
            $review->update($validatedData);

            return response()->json(['message' => 'Review updated successfully', 'review' => $review]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Review not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update review'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroyReview(Request $request)
    {
        try {

            $validatedData = $request->validate(['id' => 'required']);
            $id = $validatedData['id'];

            $review = Review::findOrFail($id);
            $review->delete();

            return response()->json(['message' => 'Review deleted successfully'], Response::HTTP_NO_CONTENT);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Review not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete review'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateReviewStatus(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'id' => 'required',
                'status' => 'required|boolean',
            ]);

            $id = $request->input('id');

            // Find review by ID
            $review = Review::findOrFail($id);

            // Update status
            $review->status = $request->input('status');
            $review->save();

            // Prepare success response
            $response = [
                'message' => 'Review status updated successfully.',
                'review' => $review
            ];

            return response()->json($response, 200);
        } catch (Exception $e) {
            // Prepare error response
            $errorResponse = [
                'message' => 'Failed to update review status.',
                'error' => $e->getMessage()
            ];

            return response()->json($errorResponse, 500);
        }
    }

    public function getRatingCounts(Request $request)
    {
        try {
            $validatedData = $request->validate(['advertisement_id' => 'required']);
            $advertisementId = $validatedData['advertisement_id'];
            $ratingCounts = Review::where('advertisement_id', $advertisementId)
                ->selectRaw('rating, count(*) as count')
                ->groupBy('rating')
                ->get()
                ->pluck('count', 'rating');

            $completeCounts = [];
            for ($i = 1; $i <= 5; $i++) {
                $completeCounts[$i] = $ratingCounts->get($i, 0);
            }

            return response()->json($completeCounts);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Advertisement not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch rating counts'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getReviewSummary(Request $request)
    {
        try {
            $request->validate([
                'advertisement_id' => 'required|exists:advertisements,id',
            ]);

            $advertisementId = $request->input('advertisement_id');

            if (!Advertisement::find($advertisementId)) {
                throw new ModelNotFoundException();
            }

            $averageRating = Review::where('advertisement_id', $advertisementId)
                ->avg('rating');
            $reviewCount = Review::where('advertisement_id', $advertisementId)
                ->count();

            return response()->json([
                'average_rating' => round($averageRating, 2),
                'review_count' => $reviewCount
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Advertisement not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch review summary'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getReviewByAdvertisementId(Request $request)
    {
        try {
            $request->validate([
                'advertisement_id' => 'required|exists:advertisements,id',
                'page' => 'nullable|integer|min:1',
                'per_page' => 'nullable|integer|min:1|max:10',
            ]);

            $advertisementId = $request->input('advertisement_id');
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 3);

            // Fetch reviews with pagination and sorting by latest
            $reviewsQuery = Review::where('advertisement_id', $advertisementId)
                ->with('user')
                ->orderBy('created_at', 'desc');

            // Calculate review count and average rating
            $reviewCount = $reviewsQuery->count();
            $avgRating = $reviewsQuery->avg('rating');

            // Fetch paginated reviews
            $paginatedReviews = $reviewsQuery->paginate($perPage, ['*'], 'page', $page);

            // Prepare response data
            $reviews = $paginatedReviews->items();
            $formattedReviews = [];
            foreach ($reviews as $review) {
                $formattedReviews[] = [
                    'rating' => $review->rating,
                    'title' => $review->title,
                    'review_text' => $review->review_text,
                    'user_name' => $review->user->name,
                    'date' => \Carbon\Carbon::parse($review->created_at)->format('d F Y'),
                ];
            }

            return response()->json([
                'review_count' => $reviewCount,
                'avg_rating' => $avgRating,
                'reviews' => $formattedReviews,
                'pagination' => [
                    'current_page' => $paginatedReviews->currentPage(),
                    'last_page' => $paginatedReviews->lastPage(),
                    'per_page' => $paginatedReviews->perPage(),
                    'total' => $paginatedReviews->total(),
                ],
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Advertisement not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch reviews'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
