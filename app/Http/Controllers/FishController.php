<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fishdata;
class FishController extends Controller
{
public function index(){
    $fishData  = Fishdata::all();
    return view('includeData', compact('fishData'));

}
}
