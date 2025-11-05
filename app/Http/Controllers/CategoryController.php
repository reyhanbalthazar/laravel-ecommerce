<?php
// app/Http/Controllers/CategoryController.php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::active()
            ->withCount(['products' => function ($query) {
                $query->where('is_active', true);
            }])
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function show(Category $category)
    {
        if (!$category->is_active) {
            abort(404);
        }

        $products = Product::where('category_id', $category->id)
            ->active()
            ->with('category')
            ->paginate(12);

        return view('categories.show', compact('category', 'products'));
    }
}
