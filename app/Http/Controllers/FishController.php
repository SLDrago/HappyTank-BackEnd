<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fishdata;


class FishController extends Controller
{

    protected $fish;

    public function __construct()
    {
        $this->fish = new FishData();
    }

    public function showFish()
    {
        $fishes = $this->fish->all();
        return view('test')->with('fishes', $fishes);
    }

    public function selectedFish(Request $request)
    {
        $fishes = $this->fish->all();

        $fish1 = $fishes->firstWhere('id', $request->input('fish1'));
        $fish2 = $fishes->firstWhere('id', $request->input('fish2'));
        $fish3 = $fishes->firstWhere('id', $request->input('fish3'));

        $result = $this->compatibilityService->checkCompatibility($fish1, $fish2, $fish3);

        return view('test')->with([
            'fishes' => $fishes,
            'selectedFish1' => $fish1,
            'selectedFish2' => $fish2,
            'selectedFish3' => $fish3,
            'result' => $result
        ]);
    }
}
