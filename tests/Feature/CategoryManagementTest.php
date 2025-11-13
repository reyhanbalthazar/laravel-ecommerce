<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_category()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        $response = $this->post('/admin/categories', [
            'name' => 'Electronics',
            'description' => 'Electronic devices and accessories',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'name' => 'Electronics',
        ]);
    }

    public function test_admin_can_edit_category()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::factory()->create(['name' => 'Old Name']);
        $this->actingAs($admin);

        $response = $this->put("/admin/categories/{$category->id}", [
            'name' => 'Updated Name',
            'description' => 'Updated description',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'name' => 'Updated Name',
        ]);
    }

    public function test_admin_can_delete_category()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::factory()->create();
        $this->actingAs($admin);

        $response = $this->delete("/admin/categories/{$category->id}");

        $response->assertRedirect();
        $this->assertSoftDeleted('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_category_listing()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Category::factory(3)->create();
        $this->actingAs($admin);

        $response = $this->get('/admin/categories');

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.index');
        $response->assertViewHas('categories');
    }

    public function test_product_can_be_filtered_by_category()
    {
        $category1 = Category::factory()->create(['name' => 'Electronics']);
        $category2 = Category::factory()->create(['name' => 'Books']);
        $product1 = Product::factory()->create(['category_id' => $category1->id]);
        $product2 = Product::factory()->create(['category_id' => $category2->id]);

        $response = $this->get("/category/{$category1->slug}");

        $response->assertStatus(200);
        $response->assertViewHas('products', function ($products) use ($product1, $product2) {
            return $products->contains($product1) && !$products->contains($product2);
        });
    }

    public function test_regular_user_cannot_access_admin_category_routes()
    {
        $user = User::factory()->create(['is_admin' => false]);
        $category = Category::factory()->create();
        $this->actingAs($user);

        $response = $this->post("/admin/categories", [
            'name' => 'Test Category',
        ]);

        $response->assertStatus(403);
    }
}