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
}
