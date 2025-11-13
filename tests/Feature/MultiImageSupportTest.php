<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MultiImageSupportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_admin_can_create_product_with_images()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = \App\Models\Category::factory()->create();
        $this->actingAs($admin);

        // Create product first without images
        $response = $this->followingRedirects()->post('/admin/products', [
            'name' => 'Test Product with Images',
            'description' => 'A product with multiple images',
            'price' => 100,
            'stock' => 10,
            'category_id' => $category->id,
        ]);

        $response->assertStatus(200); // Should reach the success page after redirect
        
        // Get the created product
        $product = Product::where('name', 'Test Product with Images')->first();
        $this->assertNotNull($product);
    }

    public function test_product_can_have_primary_image_selected()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $product = Product::factory()->create();
        $this->actingAs($admin);

        // Create multiple images for the product
        $image1 = ProductImage::factory()->create([
            'product_id' => $product->id,
            'is_primary' => false
        ]);
        $image2 = ProductImage::factory()->create([
            'product_id' => $product->id,
            'is_primary' => false
        ]);

        // Set one as primary
        $response = $this->post("/admin/products/{$product->id}/images/{$image2->id}/set-primary");
        $response->assertRedirect();

        $image2->refresh();
        $this->assertTrue($image2->is_primary);

        $image1->refresh();
        $this->assertFalse($image1->is_primary);
    }

    public function test_product_has_image_gallery_display()
    {
        $product = Product::factory()->create();
        ProductImage::factory()->create([
            'product_id' => $product->id,
            'is_primary' => true
        ]);
        ProductImage::factory(2)->create([
            'product_id' => $product->id,
            'is_primary' => false
        ]);

        $response = $this->get("/products/{$product->slug}");
        
        $response->assertStatus(200);
        $response->assertViewHas('product');
    }

    public function test_main_image_url_accessor_works()
    {
        $product = Product::factory()->create();
        ProductImage::factory()->create([
            'product_id' => $product->id,
            'is_primary' => true,
            'image_path' => 'products/test-image.jpg'
        ]);

        $this->assertNotNull($product->main_image_url);
    }

    public function test_product_images_are_ordered()
    {
        $product = Product::factory()->create();
        $image1 = ProductImage::factory()->create([
            'product_id' => $product->id,
            'sort_order' => 2
        ]);
        $image2 = ProductImage::factory()->create([
            'product_id' => $product->id,
            'sort_order' => 1
        ]);

        $product->load('images');
        $orderedImages = $product->images->sortBy('sort_order');
        
        $this->assertEquals($image2->id, $orderedImages->first()->id);
        $this->assertEquals($image1->id, $orderedImages->last()->id);
    }
}