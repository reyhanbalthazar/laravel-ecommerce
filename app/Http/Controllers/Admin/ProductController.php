<?php
// app/Http/Controllers/Admin/ProductController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // Validate main product data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Validate images if present
        if ($request->hasFile('images')) {
            $request->validate([
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ]);
        }

        // Generate unique slug
        $validated['slug'] = $this->generateUniqueSlug($validated['name']);
        $validated['sku'] = 'SKU-' . Str::upper(Str::random(8));

        $product = Product::create($validated);

        // Handle image upload
        if ($request->hasFile('images')) {
            $this->handleImageUpload($request->file('images'), $product);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        // Eager load the images relationship
        $product->load('images');
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'primary_image_id' => 'nullable|exists:product_images,id', // Add this validation
            'removed_images' => 'nullable|array'
        ]);

        // Validate images if present
        if ($request->hasFile('images')) {
            $request->validate([
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ]);
        }

        // Update slug only if name changed
        if ($product->name !== $validated['name']) {
            $validated['slug'] = $this->generateUniqueSlug($validated['name'], $product->id);
        }

        $product->update($validated);

        // Handle primary image update
        if ($request->has('primary_image_id')) {
            // Remove primary status from all images
            $product->images()->update(['is_primary' => false]);

            // Set the selected image as primary
            $primaryImage = ProductImage::find($request->primary_image_id);
            if ($primaryImage && $primaryImage->product_id === $product->id) {
                $primaryImage->update(['is_primary' => true]);
            }
        }

        // Handle removed images
        if ($request->has('removed_images')) {
            $this->handleRemovedImages($request->removed_images);
        }

        // Handle new image upload
        if ($request->hasFile('images')) {
            $this->handleImageUpload($request->file('images'), $product);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        // Delete associated images
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product moved to trash successfully!');
    }

    public function forceDestroy($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->forceDelete();
        return redirect()->route('admin.products.trashed')->with('success', 'Product permanently deleted!');
    }

    public function trashed()
    {
        $products = Product::with('category')->onlyTrashed()->latest()->paginate(10);
        return view('admin.products.trashed', compact('products'));
    }

    public function restore($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();
        return redirect()->route('admin.products.index')->with('success', 'Product restored successfully!');
    }

    /**
     * Handle image upload
     */
    private function handleImageUpload($images, $product)
    {
        $currentMaxSortOrder = $product->images()->max('sort_order') ?? -1;

        foreach ($images as $index => $image) {
            $filename = 'product_' . $product->id . '_' . time() . '_' . $index . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('products', $filename, 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'sort_order' => $currentMaxSortOrder + $index + 1,
                'is_primary' => false // Never set new images as primary
            ]);
        }
    }

    /**
     * Set an image as primary
     */
    public function setPrimaryImage(Product $product, $imageId)
    {
        // Find the image
        $image = ProductImage::findOrFail($imageId);

        // Verify the image belongs to the product
        if ($image->product_id !== $product->id) {
            return back()->with('error', 'Invalid image for this product.');
        }

        // Remove primary status from all other images of this product
        $product->images()->update(['is_primary' => false]);

        // Set this image as primary
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Primary image updated successfully!');
    }

    /**
     * Handle removed images
     */
    private function handleRemovedImages($removedImageIds)
    {
        if (!is_array($removedImageIds)) {
            return;
        }

        foreach ($removedImageIds as $imageId) {
            $image = ProductImage::find($imageId);

            if ($image) {
                // Delete file from storage
                Storage::disk('public')->delete($image->image_path);
                // Delete record from database
                $image->delete();
            }
        }
    }

    /**
     * Generate unique slug
     */
    private function generateUniqueSlug($name, $excludeId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        $query = Product::where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;

            $query = Product::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }
}
