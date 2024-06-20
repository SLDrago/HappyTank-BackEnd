<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;


class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'AquariumsAndTanks',
            'BreedingSupplies',
            'DecorationsAndSubstrate',
            'Equipment',
            'Fish',
            'Foods',
            'MaintenanceTools',
            'MedicinesAndSupplements',
            'WaterTreatmentProducts'
        ];

        foreach ($categories as $category) {
            Category::create(['name' => $category]);
        }
    }
}
