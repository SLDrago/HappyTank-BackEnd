<?php
use App\Models\Fishdata;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
Route::get('/includeData', function () {
    $fishData = Fishdata::all();
    return view('includeData' ,compact('fishData'));
});
Route::get('/includeData', [FishController::class, 'index']);
