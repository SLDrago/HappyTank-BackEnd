<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getDashboardData()
    {
        $shopCount = User::where('role', 'shop')->count();
        $userCount = User::where('role', 'user')->count();
        $advertisementCount = Advertisement::count();
        $totalVisitors = Visit::distinct('ip_address')->count();

        $currentDate = Carbon::now();

        // 1. Count of visitors for each month over the past year
        $visitorsPerMonth = Visit::selectRaw('MONTH(visited_at) as month, COUNT(DISTINCT ip_address) as total_visits')
            ->where('visited_at', '>=', Carbon::now()->subYear()) // Last 12 months
            ->groupBy('month')
            ->get();

        // 2. Count of new advertisements and removed advertisements for each day over the past week
        $advertisementsPerDay = Advertisement::selectRaw('DATE(created_at) as date, COUNT(id) as new_ads')
            ->where('created_at', '>=', $currentDate->subWeek()) // Last 7 days
            ->groupBy('date')
            ->get();

        // $removedAdvertisementsPerDay = Advertisement::selectRaw('DATE(deleted_at) as date, COUNT(id) as removed_ads')
        //     ->where('deleted_at', '>=', $currentDate->subWeek()) // Last 7 days
        //     ->onlyTrashed() // Include only soft-deleted advertisements
        //     ->groupBy('date')
        //     ->get();

        // 3. Monthly count of mobile and desktop visitors
        $deviceVisitorsPerMonth = Visit::selectRaw('MONTH(visited_at) as month, device, COUNT(DISTINCT ip_address) as total_visits')
            ->where('visited_at', '>=', Carbon::now()->subYear()) // Last 12 months
            ->groupBy('month', 'device')
            ->get();

        return response()->json([
            'shop_count' => $shopCount,
            'user_count' => $userCount,
            'advertisement_count' => $advertisementCount,
            'total_visitors' => $totalVisitors,
            'visitors_per_month' => $visitorsPerMonth,
            'new_ads_per_day' => $advertisementsPerDay,
            // 'removed_ads_per_day' => $removedAdvertisementsPerDay,
            'device_visitors_per_month' => $deviceVisitorsPerMonth,
        ]);
    }
}
