<?php

namespace App\Http\Controllers;

use App\Models\ShopInfo;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InformationController extends Controller
{
    /**
     * Check if shop information exists for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function hasShopInfo(Request $request)
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        // Check if shop information exists for the user
        $hasShopInfo = ShopInfo::where('user_id', $user->id)->exists();

        // Return true or false based on whether shop information exists
        return response()->json(['has_shop_info' => $hasShopInfo], 200);
    }

    /**
     * Get shop information for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getShopInfo(Request $request)
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        // Check if shop information exists for the user
        $shopInfo = ShopInfo::where('user_id', $user->id)->first();

        if (!$shopInfo) {
            // Shop information not found, return appropriate response
            return response()->json(['error' => 'Shop information not found for this user'], 404);
        }

        // Shop information found, return it
        return response()->json(['shop_info' => $shopInfo], 200);
    }

    /**
     * Update shop information for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateShopInfo(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'owner_name' => 'required|string',
            'description' => 'nullable|string',
            'physical_address' => 'required|string',
            'phone_number' => 'required|string',
            'category' => 'required|string',
            'gps_coordinates' => 'required|array',
            'working_hours' => 'required|array',
            'socialmedia_links' => 'nullable|array',
        ]);

        // Retrieve the authenticated user
        $user = Auth::user();

        // Check if shop information already exists for the user
        $shopInfo = ShopInfo::where('user_id', $user->id)->first();

        if (!$shopInfo) {
            // Shop information not found, create a new entry
            $shopInfo = new ShopInfo();
            $shopInfo->user_id = $user->id;
        }

        // Update shop information
        $shopInfo->fill($validatedData);
        $shopInfo->save();

        // Return success response
        return response()->json(['message' => 'Shop information updated successfully'], 200);
    }

    public function hasUserInfo(Request $request)
    {
        $user = Auth::user();
        $hasUserInfo = UserInfo::where('user_id', $user->id)->exists();
        return response()->json(['has_user_info' => $hasUserInfo], 200);
    }

    public function getUserInfo(Request $request)
    {
        $user = Auth::user();
        $userInfo = UserInfo::where('user_id', $user->id)->first();
        if (!$userInfo) {
            return response()->json(['error' => 'User information not found for this user'], 404);
        }
        return response()->json(['user_info' => $userInfo], 200);
    }

    public function updateUserInfo(Request $request)
    {
        $validatedData = $request->validate([
            'phone_number' => 'required|string'
        ]);
        $user = Auth::user();
        $userInfo = UserInfo::where('user_id', $user->id)->first();

        if (!$userInfo) {
            $userInfo = new UserInfo();
            $userInfo->user_id = $user->id;
        }
        $userInfo->fill($validatedData);
        $userInfo->save();
        return response()->json(['message' => 'User information updated successfully'], 200);
    }
}
