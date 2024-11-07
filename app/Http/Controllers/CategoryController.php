<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        
        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
                'message' => 'Categories retrieved successfully',
            ],
            'data' => $categories,
        ]);
    }

    public function show($categorySlug)
    {
        $category = Category::where('slug', $categorySlug)->first();

        if ($category)
        {
            $articles = Category::find($category->id)
                ->articles()
                ->with(['category' , 'user:id,name,picture'])
                ->select([
                    'id', 'user_id', 'category_id', 'title', 'slug', 'content_preview','featured_image', 'created_at', 'updated_at',
                ])
                ->paginate();
            
            return response()->json([
                'meta' => [
                    'code' => '200',
                    'status' => 'success',
                    'message' => 'Category retrieved successfully',
                ],
                'data' => $articles,
            ], 200);
        }

        return response()->json([
            'meta' => [
                'code' => '404',
                'status' => 'error',
                'message' => 'Category not found. ',
            ],
            'data' =>[]
        ], 404);
    }
}
