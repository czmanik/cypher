<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Filament\Resources\RecipeResource;

class RecipeResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_access_edit_page_with_multiple_roles()
    {
        $user = User::factory()->create([
            'is_manager' => true,
            'is_active' => true,
            'employee_type' => User::TYPE_MANAGER,
        ]);

        $category = Category::create(['name' => 'Food', 'slug' => 'food', 'is_active' => true]);
        $product = Product::create([
            'name' => 'Test Product',
            'category_id' => $category->id,
            'price' => 100,
            'is_available' => true,
        ]);

        $recipe = Recipe::create([
            'product_id' => $product->id,
            'allowed_roles' => [User::TYPE_KITCHEN, User::TYPE_MANAGER], // Multiple roles!
            'description' => 'Test',
        ]);

        $this->actingAs($user)
            ->get(RecipeResource::getUrl('edit', ['record' => $recipe]))
            ->assertSuccessful();

        // Also verify List page doesn't crash (returns 200 OK), even if we don't assert content
        $this->actingAs($user)
            ->get(RecipeResource::getUrl('index'))
            ->assertSuccessful();
    }
}
