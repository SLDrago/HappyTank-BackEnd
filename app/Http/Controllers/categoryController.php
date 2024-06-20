<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function addCategories(Request $request)
    {
        $validatedData = $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'string'
        ]);

        try {
            $categories = [];

            foreach ($validatedData['categories'] as $categoryName) {
                $category = Category::create(['name' => $categoryName]);
                $categories[] = $category;
            }

            return response()->json([
                'message' => 'Categories added successfully',
                'data' => $categories
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to add categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCategories()
    {
        $categories = Category::all(['id', 'name']);

        return response()->json([
            'data' => $categories
        ], 200);
    }

    public function updateCategory(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id' => 'required|integer',
                'name' => 'required|string'
            ]);

            $id = $validatedData['id'];

            $category = Category::findOrFail($id);
            $category->name = $validatedData['name'];
            $category->save();

            return response()->json([
                'message' => 'Category updated successfully',
                'data' => $category
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteCategory(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id' => 'required|integer'
            ]);

            $id = $validatedData['id'];
            $category = Category::findOrFail($id);
            $category->delete();

            return response()->json([
                'message' => 'Category deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
