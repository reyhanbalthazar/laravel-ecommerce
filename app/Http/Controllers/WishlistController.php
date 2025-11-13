<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\WishlistItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /**
     * Display the wishlist page.
     */
    public function index(): View
    {
        $wishlist = auth()->user()->wishlist;

        if (!$wishlist) {
            $wishlist = auth()->user()->wishlist()->create();
        }

        $products = $wishlist->products()->with('images')->paginate(12);

        return view('wishlist.index', compact('products'));
    }

    /**
     * Add a product to the wishlist.
     */
    public function add($product): RedirectResponse
    {
        // Find the product by ID
        $productModel = \App\Models\Product::find($product);
        
        if (!$productModel) {
            return redirect()->back()->with('error', 'Product not found!');
        }
        
        $wishlist = auth()->user()->wishlist;

        if (!$wishlist) {
            $wishlist = auth()->user()->wishlist()->create();
        }

        // Check if product is already in wishlist
        $existingItem = $wishlist->items()->where('product_id', $productModel->id)->first();

        if (!$existingItem) {
            $wishlist->items()->create([
                'product_id' => $productModel->id,
            ]);

            return redirect()->back()->with('success', 'Product added to wishlist!');
        }

        return redirect()->back()->with('info', 'Product is already in your wishlist!');
    }

    /**
     * Remove a product from the wishlist.
     */
    public function remove($product): RedirectResponse
    {
        // Find the product by ID
        $productModel = \App\Models\Product::find($product);
        
        if (!$productModel) {
            return redirect()->back()->with('error', 'Product not found!');
        }
        
        $wishlist = auth()->user()->wishlist;

        if ($wishlist) {
            $wishlistItem = $wishlist->items()->where('product_id', $productModel->id)->first();

            if ($wishlistItem) {
                $wishlistItem->delete();
                return redirect()->back()->with('success', 'Product removed from wishlist!');
            }
        }

        return redirect()->back()->with('error', 'Product not found in wishlist!');
    }

    /**
     * Clear all items from the wishlist.
     */
    public function clear(): RedirectResponse
    {
        $wishlist = auth()->user()->wishlist;

        if ($wishlist) {
            $wishlist->items()->delete();
            return redirect()->route('wishlist.index')->with('success', 'Wishlist cleared successfully!');
        }

        return redirect()->route('wishlist.index')->with('info', 'Your wishlist is already empty!');
    }

    /**
     * Toggle product in wishlist (AJAX).
     */
    public function toggle($product): \Illuminate\Http\JsonResponse
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
                'redirect' => route('login')
            ], 401);
        }

        // Find the product by ID
        $productModel = \App\Models\Product::find($product);
        
        if (!$productModel) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $wishlist = auth()->user()->wishlist;

        if (!$wishlist) {
            $wishlist = auth()->user()->wishlist()->create();
        }

        $existingItem = $wishlist->items()->where('product_id', $productModel->id)->first();

        if ($existingItem) {
            // Remove from wishlist
            $existingItem->delete();
            return response()->json([
                'success' => true,
                'action' => 'removed',
                'message' => 'Product removed from wishlist',
                'in_wishlist' => false
            ]);
        } else {
            // Add to wishlist
            $wishlist->items()->create([
                'product_id' => $productModel->id,
            ]);
            return response()->json([
                'success' => true,
                'action' => 'added',
                'message' => 'Product added to wishlist',
                'in_wishlist' => true
            ]);
        }
    }

    /**
     * Get wishlist count (AJAX).
     */
    public function count(): \Illuminate\Http\JsonResponse
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json([
                'count' => 0,
                'success' => false,
                'message' => 'Authentication required',
                'redirect' => route('login')
            ], 401);
        }

        $wishlist = auth()->user()->wishlist;

        if ($wishlist) {
            $count = $wishlist->items()->count();
        } else {
            $count = 0;
        }

        return response()->json([
            'count' => $count,
            'success' => true
        ]);
    }
}
