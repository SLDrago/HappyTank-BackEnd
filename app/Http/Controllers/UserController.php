<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\ShopInfo;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateNameEmail(Request $request)
    {
        $user = User::findOrFail($request->user()->id);
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validatedData);

        return response()->json(['message' => 'User details updated successfully', 'user' => $user], 200);
    }

    public function destroy(Request $request)
    {
        $user = User::findOrFail($request->user()->id);
        $user->delete();

        return response()->json(['message' => 'User removed successfully'], 200);
    }

    public function updateProfilePicture(Request $request)
    {
        $user = User::findOrFail($request->user()->id);
        $validatedData = $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $profilePhotoPath = $request->file('profile_photo')->store('profile_photos', 'public');

        if ($profilePhotoPath) {
            $profilePhotoUrl = Storage::url($profilePhotoPath);
            if ($user->profile_photo_path) {
                $relativePath = str_replace('/storage/', '', $user->profile_photo_path);
                Storage::disk('public')->delete($relativePath);
            }

            $user->profile_photo_path = $profilePhotoUrl;
            $user->save();

            return response()->json(['message' => 'Profile picture updated successfully', 'user' => $user], 200);
        }

        return response()->json(['message' => 'Failed to upload new profile picture'], 500);
    }


    public function updateBannerPhoto(Request $request)
    {
        $user = User::findOrFail($request->user()->id);
        $validatedData = $request->validate([
            'banner_photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $bannerPhotoPath = $request->file('banner_photo')->store('banner_photos', 'public');

        if ($bannerPhotoPath) {
            $bannerPhotoUrl = Storage::url($bannerPhotoPath);
            if ($user->banner_photo_path) {
                $relativePath = str_replace('/storage/', '', $user->banner_photo_path);
                Storage::disk('public')->delete($relativePath);
            }

            $user->banner_photo_path = $bannerPhotoUrl;
            $user->save();

            return response()->json(['message' => 'Banner photo updated successfully', 'user' => $user], 200);
        }

        return response()->json(['message' => 'Failed to upload new banner photo'], 500);
    }

    public function updatePassword(Request $request)
    {
        try {
            $user = User::findOrFail($request->user()->id);

            $validatedData = $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            if (!Hash::check($validatedData['current_password'], $user->password)) {
                return response()->json(['message' => 'Current password is incorrect'], 403);
            }

            $user->password = Hash::make($validatedData['new_password']);
            $user->save();

            return response()->json(['message' => 'Password updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update password'], 500);
        }
    }


    public function getSellerCardDetails(Request $request)
    {
        $user = User::findOrFail($request->id);
        if ($user->role == 'shop') {
            $additionalData = ShopInfo::where('user_id', $user->id)->first();
        } else {
            $additionalData = UserInfo::where('user_id', $user->id)->first();
        }

        $city = City::where('id', $additionalData->city_id)->first();
        $address = $city->name;

        $response[] = [
            'id' => $user->id ?? null,
            'name' => $user->name ?? null,
            'email' => $user->email ?? null,
            'profile_photo_path' => $user->profile_photo_path ?? null,
            'profile_photo_url' => $user->profile_photo_url ?? null,
            'address' => $additionalData->address ?? null,
            'description' => $additionalData->description ?? null,
            'gps' => $additionalData->gps_coordinates ?? null,
            'city' => $address
        ];
        return response()->json($response, 200);
    }
}
