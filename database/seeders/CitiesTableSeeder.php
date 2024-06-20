<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\City;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $cities = [
            'Colombo',
            'Mount Lavinia',
            'Kesbewa',
            'Maharagama',
            'Moratuwa',
            'Ratnapura',
            'Negombo',
            'Kandy',
            'Sri Jayewardenepura Kotte',
            'Kalmunai',
            'Trincomalee',
            'Galle',
            'Jaffna',
            'Athurugiriya',
            'Weligama',
            'Matara',
            'Kolonnawa',
            'Gampaha',
            'Puttalam',
            'Badulla',
            'Kalutara',
            'Bentota',
            'Mannar',
            'Kurunegala'
        ];

        foreach ($cities as $city) {
            City::create(['name' => $city]);
        }
    }
}
