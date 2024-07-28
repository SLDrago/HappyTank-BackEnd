<?php

namespace App\Http\Controllers;

use App\Models\UserInfo;
use App\Models\ShopInfo;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\AdvertisementImage;
use Illuminate\Support\Facades\DB;
use Exception;

class AdvertisementController extends Controller
{
    protected $advertisement;

    public function __construct()
    {
        $this->advertisement = new Advertisement();
    }

    public function get_allAdvertisements(Request $request)
    {
        $advertisements = $this->advertisement->all();
        return json_encode($advertisements);
    }

    public function get_topAdvertisements(Request $request)
    {
        // Fetch top advertisements based on views
        $topAdvertisements = $this->advertisement->orderBy('views', 'desc')->take(6)->get();
        return response()->json($topAdvertisements);
    }

    // public function addAdvertisement(Request $request)
    // {
    //     try {
    //         // Validate request fields
    //         $fields = $request->validate([
    //             'title' => 'required|string',
    //             'small_description' => 'required|string',
    //             'description' => 'required|string',
    //             'image_url.*' => 'required|file|image|max:10240', // Allow multiple images
    //             'price' => 'required|numeric',
    //             'price_based_on' => 'required|string',
    //             'category_id' => 'required|exists:categories,id',
    //             'tags' => 'required|string'
    //         ]);

    //         // Get authenticated user ID
    //         $user = Auth::user();
    //         $user_id = $user->id;

    //         // Create advertisement
    //         $advertisement = Advertisement::create([
    //             'title' => $fields['title'],
    //             'small_description' => $fields['small_description'],
    //             'description' => $fields['description'],
    //             'price' => $fields['price'],
    //             'price_based_on' => $fields['price_based_on'],
    //             'category_id' => $fields['category_id'],
    //             'tags' => $fields['tags'],
    //             'user_id' => $user_id
    //         ]);

    //         // Handle image uploads to GCS
    //         if ($request->hasFile('image_url')) {
    //             foreach ($request->file('image_url') as $file) {
    //                 // Store file in GCS and log file path
    //                 $imagePath = Storage::disk('gcs')->put('advertisement_images', $file);
    //                 if ($imagePath) {
    //                     Log::info("Stored file path: " . $imagePath);

    //                     // Generate public URL for the file and log URL
    //                     $imageUrl = Storage::disk('gcs')->url($imagePath);
    //                     Log::info("File URL: " . $imageUrl);

    //                     // Create advertisement image record and log database action
    //                     AdvertisementImage::create([
    //                         'advertisement_id' => $advertisement->id,
    //                         'image_url' => $imageUrl
    //                     ]);
    //                     Log::info("Stored image URL in database for advertisement ID: " . $advertisement->id);
    //                 } else {
    //                     Log::error("Failed to store file on GCS for advertisement ID: " . $advertisement->id);
    //                 }
    //             }
    //         }

    //         // Prepare success response
    //         $response = [
    //             'advertisement' => $advertisement,
    //             'message' => 'Advertisement added successfully.'
    //         ];

    //         return response($response, 201);
    //     } catch (Exception $e) {
    //         // Log error for debugging
    //         Log::error("Error adding advertisement: " . $e->getMessage());

    //         // Prepare error response
    //         $errorResponse = [
    //             'message' => 'Failed to add advertisement.',
    //             'error' => $e->getMessage()
    //         ];

    //         return response($errorResponse, 500);
    //     }
    // }

    public function addAdvertisement(Request $request)
    {
        try {
            $fields = $request->validate([
                'title' => 'required|string',
                'small_description' => 'required|string',
                'description' => 'required|string',
                'image_url.*' => 'required|file|image|max:10240',
                'price' => 'required|numeric',
                'price_based_on' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'tags' => 'required|string'
            ]);

            $user = Auth::user();
            $user_id = $user->id;

            if ($user->role === 'shop') {
                $shopInfo = ShopInfo::where('user_id', $user_id)->first();
                if (!$shopInfo) {
                    return response()->json(['message' => 'Shop information not found'], 404);
                }
            } else if ($user->role === 'user') {
                $userInfo = UserInfo::where('user_id', $user_id)->first();
                $advertisementCount = Advertisement::where('user_id', $user_id)->count();
                if (!$userInfo) {
                    return response()->json(['message' => 'User information not found'], 404);
                } else if ($advertisementCount >= 2) {
                    return response()->json(['message' => 'You have reached the maximum number of advertisements'], 403);
                }
            }

            $advertisement = Advertisement::create([
                'title' => $fields['title'],
                'small_description' => $fields['small_description'],
                'description' => $fields['description'],
                'price' => $fields['price'],
                'price_based_on' => $fields['price_based_on'],
                'category_id' => $fields['category_id'],
                'tags' => $fields['tags'],
                'user_id' => $user_id
            ]);

            if ($request->hasFile('image_url')) {
                foreach ($request->file('image_url') as $file) {
                    try {
                        $imagePath = $file->store('advertisement_images', 'public');
                        if ($imagePath) {
                            $imageUrl = Storage::url($imagePath);

                            AdvertisementImage::create([
                                'advertisement_id' => $advertisement->id,
                                'image_url' => $imageUrl
                            ]);
                        } else {
                            $errorResponse = [
                                'message' => 'Failed to store images.'
                            ];

                            return response($errorResponse, 500);
                        }
                    } catch (Exception $e) {
                        $errorResponse = [
                            'message' => 'Failed to store images.',
                            'error' => $e->getMessage()
                        ];

                        return response($errorResponse, 500);
                    }
                }
            }

            $response = [
                'advertisement' => $advertisement,
                'message' => 'Advertisement added successfully.'
            ];

            return response($response, 201);
        } catch (Exception $e) {
            $errorResponse = [
                'message' => 'Failed to add advertisement.',
                'error' => $e->getMessage()
            ];

            return response($errorResponse, 500);
        }
    }

    public function updateAdvertisement(Request $request)
    {
        try {
            // Validate request fields
            $fields = $request->validate([
                'id' => 'required',
                'title' => 'sometimes|required|string',
                'small_description' => 'sometimes|required|string',
                'description' => 'sometimes|required|string',
                'price' => 'sometimes|required|numeric',
                'price_based_on' => 'sometimes|required|string',
                'category_id' => 'sometimes|required|exists:categories,id',
                'tags' => 'sometimes|required|string'
            ]);

            $id = $fields['id'];
            $advertisement = Advertisement::findOrFail($id);

            if ($advertisement->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $advertisement->update($fields);

            return response()->json(['message' => 'Advertisement updated successfully', 'advertisement' => $advertisement], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update advertisement', 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteAdvertisement(Request $request)
    {
        try {
            $fields = $request->validate([
                'id' => 'required'
            ]);

            $id = $fields['id'];

            $advertisement = Advertisement::findOrFail($id);

            if ($advertisement->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $images = AdvertisementImage::where('advertisement_id', $id)->get();
            foreach ($images as $image) {
                $relativePath = str_replace('/storage/', '', $image->image_url);
                Storage::disk('public')->delete($relativePath);
                $image->delete();
            }

            $advertisement->delete();

            return response()->json(['message' => 'Advertisement deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to delete advertisement', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateAdvertisementWithImages(Request $request)
    {
        try {
            // Validate request fields
            $fields = $request->validate([
                'id' => 'sometimes|required',
                'title' => 'sometimes|required|string',
                'small_description' => 'sometimes|required|string',
                'description' => 'sometimes|required|string',
                'image_url' => 'sometimes|file|image|max:10240', // Assuming a max size of 10MB for the image
                'price' => 'sometimes|required|numeric',
                'price_based_on' => 'sometimes|required|string',
                'category_id' => 'sometimes|required|exists:categories,id',
                'tags' => 'sometimes|required|string'
            ]);

            $id = $fields['id'];
            // Find the advertisement by ID
            $advertisement = Advertisement::findOrFail($id);

            // Check if the authenticated user is the owner
            if ($advertisement->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            // Update the advertisement with new data
            $advertisement->update($fields);

            // Handle image upload to GCS if provided
            if ($request->hasFile('image_url')) {
                // Delete the old image
                $oldImage = AdvertisementImage::where('advertisement_id', $advertisement->id)->first();
                if ($oldImage) {
                    Storage::disk('gcs')->delete($oldImage->image_url);
                    $oldImage->delete();
                }

                // Store new image
                $imagePath = $request->file('image_url')->store('advertisement_images', 'gcs');

                // Manually construct the public URL
                $bucketName = env('GCS_BUCKET');
                $imageUrl = "https://storage.googleapis.com/{$bucketName}/{$imagePath}";

                // Create new advertisement image
                AdvertisementImage::create([
                    'advertisement_id' => $advertisement->id,
                    'image_url' => $imageUrl
                ]);
            }

            return response()->json(['message' => 'Advertisement updated successfully', 'advertisement' => $advertisement], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update advertisement', 'error' => $e->getMessage()], 500);
        }
    }

    public function getUserAdvertisements(Request $request)
    {
        try {
            // Get authenticated user ID
            $user_id = Auth::id();

            // Fetch user's advertisements with required fields
            $advertisements = Advertisement::where('user_id', $user_id)
                ->with(['category', 'images'])
                ->get(['id', 'title', 'small_description', 'category_id']);

            // Prepare response with required fields and associated category name
            $response = $advertisements->map(function ($advertisement) {
                return [
                    'id' => $advertisement->id,
                    'title' => $advertisement->title,
                    'small_description' => $advertisement->small_description,
                    'category' => $advertisement->category->name,
                    'image_url' => $advertisement->images->isNotEmpty() ? $advertisement->images->first()->image_url : null
                ];
            });

            return response()->json($response, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to retrieve user advertisements', 'error' => $e->getMessage()], 500);
        }
    }

    public function getAdvertisementById(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'id' => 'required|exists:advertisements,id'
            ]);

            // Extract advertisement ID from the request
            $advertisementId = $request->id;

            // Find the advertisement by ID with its associated images
            $advertisement = Advertisement::with('user', 'images')->findOrFail($advertisementId);

            // Extract user information from the users table
            $user = $advertisement->user;
            $userInformation = [
                'profile_photo_path' => $user->profile_photo_path,
                'email' => $user->email,
                'name' => $user->name,
            ];

            // Extract user additional information based on role (User or Shop)
            if ($user->role === 'user') {
                $additionalInformation = UserInfo::where('user_id', $user->id)->first();
            } elseif ($user->role === 'shop') {
                $additionalInformation = ShopInfo::where('user_id', $user->id)->first();
            }

            // Prepare response with advertisement data, user basic information, and additional information
            $response = [
                'advertisement' => $advertisement,
                'user_information' => array_merge($userInformation, $additionalInformation ? $additionalInformation->toArray() : []),
            ];

            return response()->json($response, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to retrieve advertisement data', 'error' => $e->getMessage()], 500);
        }
    }

    public function getAdvertisementImagesByAdId(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'advertisement_id' => 'required|exists:advertisements,id'
            ]);

            // Extract advertisement ID from the request
            $advertisementId = $request->advertisement_id;

            // Find the advertisement by ID with its associated images
            $advertisement = Advertisement::with('images')->findOrFail($advertisementId);

            // Extract image URLs from the images relationship
            $imageUrls = $advertisement->images->map(function ($image) {
                return ['id' => $image->id, 'img_url' => $image->image_url];
            });

            return response()->json(['image_urls' => $imageUrls], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to retrieve advertisement images', 'error' => $e->getMessage()], 500);
        }
    }

    public function getTopRatedAdvertisements(Request $request)
    {
        try {
            // Query to get advertisements with the highest ratings
            $topRatedAdvertisements = Advertisement::with('images') // Eager load images relationship
                ->leftJoin('reviews', 'advertisements.id', '=', 'reviews.advertisement_id')
                ->select('advertisements.id', 'advertisements.title', 'advertisements.small_description', 'advertisements.price', 'advertisements.created_at', 'advertisements.updated_at', DB::raw('AVG(reviews.rating) as avg_rating'))
                ->groupBy('advertisements.id', 'advertisements.title', 'advertisements.small_description', 'advertisements.price', 'advertisements.created_at', 'advertisements.updated_at')
                ->orderByDesc('avg_rating')
                ->limit(6) // Limit the number of top-rated advertisements
                ->get();

            // Prepare response with one image for each advertisement
            $response = [];
            foreach ($topRatedAdvertisements as $advertisement) {
                $image = $advertisement->images->isNotEmpty() ? $advertisement->images->first()->image_url : null;
                $response[] = [
                    'id' => $advertisement->id,
                    'title' => $advertisement->title,
                    'small_description' => $advertisement->small_description,
                    'price' => $advertisement->price,
                    'rating' => $advertisement->avg_rating,
                    'image_url' => $image,
                ];
            }

            return response()->json(['top_rated_advertisements' => $response], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to retrieve top-rated advertisements', 'error' => $e->getMessage()], 500);
        }
    }

    public function loadAdvertisementsByCategory(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'category' => 'required|string',
                'page' => 'nullable|integer|min:1',
                'per_page' => 'nullable|integer|min:1|max:10', // Limiting to 10 advertisements per page for demonstration
            ]);

            // Extract parameters from the request
            $category = $request->category;
            $page = $request->page ?? 1;
            $perPage = $request->per_page ?? 5; // Default to 5 advertisements per page

            // Query advertisements filtered by category
            $advertisements = Advertisement::where('category', $category)
                ->with('images') // Eager load images relationship
                ->orderBy('created_at', 'desc') // Order by creation date, you can change this as per your preference
                ->paginate($perPage, ['*'], 'page', $page); // Paginate the results

            // Prepare response with advertisement data and image URLs
            $response = [];
            foreach ($advertisements as $advertisement) {
                $image = $advertisement->images->isNotEmpty() ? $advertisement->images->first()->image_url : null;
                $response[] = [
                    'advertisement' => $advertisement,
                    'image_url' => $image,
                ];
            }

            return response()->json(['advertisements' => $response], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to load advertisements', 'error' => $e->getMessage()], 500);
        }
    }

    public function filterAdvertisements(Request $request)
    {
        try {
            $request->validate([
                'search' => 'nullable|string',
                'category' => 'nullable|string',
                'city' => 'nullable|string',
                'min_price' => 'nullable|numeric|min:0',
                'max_price' => 'nullable|numeric|min:0',
                'page' => 'nullable|integer|min:1',
                'per_page' => 'nullable|integer|min:6',
            ]);

            $search = $request->search;
            $category = $request->category;
            $city = $request->city;
            $minPrice = $request->min_price;
            $maxPrice = $request->max_price;
            $page = $request->page ?? 1;
            $perPage = $request->per_page ?? 6;

            $query = Advertisement::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%$search%")
                        ->orWhere('small_description', 'like', "%$search%")
                        ->orWhere('tags', 'like', "%$search%")
                        ->orWhere('description', 'like', "%$search%");
                });
            }

            if ($category) {
                $query->where('category_id', $category);
            }

            if ($city) {
                $userIds = UserInfo::where('city_id', $city)->pluck('user_id')
                    ->merge(ShopInfo::where('city_id', $city)->pluck('user_id'))
                    ->unique();

                $query->whereIn('user_id', $userIds);
            }

            if ($minPrice !== null) {
                $query->where('price', '>=', $minPrice);
            }
            if ($maxPrice !== null) {
                $query->where('price', '<=', $maxPrice);
            }

            $query->withCount('reviews');
            $query->withAvg('reviews', 'rating');

            $advertisements = $query->with('images')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $response = [];
            foreach ($advertisements as $advertisement) {
                $image = $advertisement->images->isNotEmpty() ? $advertisement->images->first()->image_url : null;
                $response[] = [
                    'id' => $advertisement->id,
                    'title' => $advertisement->title,
                    'small_description' => $advertisement->small_description,
                    'price' => $advertisement->price,
                    'image_url' => $image,
                    'avg_review' => $advertisement->reviews_avg_rating ?? 0, // If no reviews, default to 0
                    'review_count' => $advertisement->reviews_count ?? 0, // If no reviews, default to 0
                ];
            }

            return response()->json([
                'advertisements' => $response,
                'meta' => [
                    'current_page' => $advertisements->currentPage(),
                    'last_page' => $advertisements->lastPage(),
                    'per_page' => $advertisements->perPage(),
                    'total' => $advertisements->total(),
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to filter advertisements', 'error' => $e->getMessage()], 500);
        }
    }


    public function searchRelatedAdvertisements(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'tags' => 'required|string',
                'city' => 'nullable|string',
                'page' => 'nullable|integer|min:1',
                'per_page' => 'nullable|integer|min:1|max:12', // Limiting to 12 advertisements per page
            ]);

            // Extract parameters from the request
            $tags = $request->tags;
            $city = $request->city;
            $page = $request->page ?? 1;
            $perPage = $request->per_page ?? 6; // Default to 6 advertisements per page

            // Split tags into an array
            $tagsArray = array_map('trim', explode(',', $tags));

            // Start building the query with tags
            $query = Advertisement::query();
            $query->where(function ($q) use ($tagsArray) {
                foreach ($tagsArray as $tag) {
                    $q->orWhere('tags', 'like', "%$tag%")
                        ->orWhere('title', 'like', "%$tag%")
                        ->orWhere('small_description', 'like', "%$tag%")
                        ->orWhere('description', 'like', "%$tag%");
                }
            });

            // Apply city filter if provided
            if ($city) {
                // Fetch user IDs based on city from UserInfo and ShopInfo
                $userIds = UserInfo::where('city', $city)->pluck('user_id')
                    ->merge(ShopInfo::where('city', $city)->pluck('user_id'))
                    ->unique();

                // Apply the user IDs filter to the query
                $query->whereIn('user_id', $userIds);
            }

            // Eager load reviews relationship
            $query->withCount('reviews');
            $query->withAvg('reviews', 'rating');

            // Get results
            $advertisements = $query->with('images')
                ->orderBy('created_at', 'desc') // Order by creation date, you can change this as per your preference
                ->paginate($perPage, ['*'], 'page', $page);

            // Check if we have results for tags + city
            if ($advertisements->isEmpty() && $city) {
                // No results found for city, search only by tags
                $query = Advertisement::query();
                $query->where(function ($q) use ($tagsArray) {
                    foreach ($tagsArray as $tag) {
                        $q->orWhere('tags', 'like', "%$tag%")
                            ->orWhere('title', 'like', "%$tag%")
                            ->orWhere('small_description', 'like', "%$tag%")
                            ->orWhere('description', 'like', "%$tag%");
                    }
                });

                // Eager load reviews relationship
                $query->withCount('reviews');
                $query->withAvg('reviews', 'rating');

                // Get results
                $advertisements = $query->with('images')
                    ->orderBy('created_at', 'desc') // Order by creation date, you can change this as per your preference
                    ->paginate($perPage, ['*'], 'page', $page);
            }

            // Prepare response with advertisement data and additional information
            $response = [];
            foreach ($advertisements as $advertisement) {
                $image = $advertisement->images->isNotEmpty() ? $advertisement->images->first()->image_url : null;
                $response[] = [
                    'title' => $advertisement->title,
                    'small_description' => $advertisement->small_description,
                    'price' => $advertisement->price,
                    'image_url' => $image,
                    'avg_review' => $advertisement->reviews_avg_rating ?? 0, // If no reviews, default to 0
                    'review_count' => $advertisement->reviews_count ?? 0, // If no reviews, default to 0
                ];
            }

            return response()->json(['advertisements' => $response], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to search advertisements', 'error' => $e->getMessage()], 500);
        }
    }


    public function setAdvertisementStatus(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'advertisement_id' => 'required|exists:advertisements,id',
                'status' => 'required|boolean',
            ]);

            // Extract parameters from the request
            $advertisementId = $request->advertisement_id;
            $status = $request->status;

            // Find the advertisement
            $advertisement = Advertisement::find($advertisementId);

            if (!$advertisement) {
                return response()->json(['message' => 'Advertisement not found'], 404);
            }

            // Update the status
            $advertisement->status = $status;
            $advertisement->save();

            // Prepare success response
            return response()->json(['message' => 'Advertisement status updated successfully'], 200);
        } catch (Exception $e) {
            // Prepare error response
            return response()->json(['message' => 'Failed to update advertisement status', 'error' => $e->getMessage()], 500);
        }
    }

    public function searchRelatedFishAdvertisements(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'fish_name' => 'required|string',
                'page' => 'nullable|integer|min:1',
                'per_page' => 'nullable|integer|min:6',
            ]);

            // Extract parameters from the request
            $fishNames = $request->fish_name;
            $page = $request->page ?? 1;
            $perPage = $request->per_page ?? 6;

            // Split fish names into an array
            $fishNamesArray = array_map('trim', explode(' ', $fishNames));

            // Start building the query
            $query = Advertisement::query();

            // Apply search by fish names
            $query->where(function ($q) use ($fishNamesArray) {
                foreach ($fishNamesArray as $fishName) {
                    $q->orWhere('title', 'like', "%$fishName%")
                        ->orWhere('small_description', 'like', "%$fishName%")
                        ->orWhere('tags', 'like', "%$fishName%")
                        ->orWhere('description', 'like', "%$fishName%");
                }
            });

            // Eager load reviews relationship
            $query->withCount('reviews');
            $query->withAvg('reviews', 'rating');

            // Paginate the results
            $advertisements = $query->with('images')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            // Prepare response with advertisement data and additional information
            $response = [];
            foreach ($advertisements as $advertisement) {
                $image = $advertisement->images->isNotEmpty() ? $advertisement->images->first()->image_url : null;
                $response[] = [
                    'id' => $advertisement->id,
                    'title' => $advertisement->title,
                    'small_description' => $advertisement->small_description,
                    'price' => $advertisement->price,
                    'image_url' => $image,
                    'avg_review' => $advertisement->reviews_avg_rating ?? 0, // If no reviews, default to 0
                    'review_count' => $advertisement->reviews_count ?? 0, // If no reviews, default to 0
                ];
            }

            return response()->json([
                'advertisements' => $response,
                'meta' => [
                    'current_page' => $advertisements->currentPage(),
                    'last_page' => $advertisements->lastPage(),
                    'per_page' => $advertisements->perPage(),
                    'total' => $advertisements->total(),
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to search advertisements by fish names', 'error' => $e->getMessage()], 500);
        }
    }

    public function getUsersAdvertisementCount(Request $request)
    {
        try {
            $user_id = Auth::id();

            $advertisementCount = Advertisement::where('user_id', $user_id)->count();

            return response()->json(['advertisement_count' => $advertisementCount], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to retrieve user advertisements count', 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteAdvertisementImage(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'image_id' => 'required|exists:advertisement_images,id',
            ]);

            // Extract image ID from the request
            $imageId = $request->image_id;

            // Find the image by ID
            $image = AdvertisementImage::findOrFail($imageId);

            // Delete the image from public storage
            Storage::disk('public')->delete($image->image_url);

            // Delete the image record
            $image->delete();

            return response()->json(['message' => 'Advertisement image deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to delete advertisement image', 'error' => $e->getMessage()], 500);
        }
    }

    public function AddAdvertisementImage(Request $request)
    {
        try {
            $request->validate([
                'advertisement_id' => 'required|exists:advertisements,id',
                'image_url' => 'required|file|image|max:10240', // Assuming a max size of 10MB for the image
            ]);

            $advertisementId = $request->advertisement_id;
            $image = $request->file('image_url');

            $advertisement = Advertisement::findOrFail($advertisementId);

            if ($advertisement->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $imagePath = $image->store('advertisement_images', 'public');

            $imageUrl = Storage::url($imagePath);

            AdvertisementImage::create([
                'advertisement_id' => $advertisementId,
                'image_url' => $imageUrl
            ]);

            return response()->json(['message' => 'Advertisement image added successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to add advertisement image', 'error' => $e->getMessage()], 500);
        }
    }
}
