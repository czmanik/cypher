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
            'name' => 'Test Recipe',
            'product_id' => $product->id,
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

    public function test_active_employee_can_view_recipes()
    {
        $user = User::factory()->create([
            'is_manager' => false,
            'is_active' => true,
            'employee_type' => User::TYPE_KITCHEN,
        ]);

        Recipe::create([
            'name' => 'General Recipe',
            'description' => 'For everyone',
        ]);

        $this->actingAs($user)
            ->get(RecipeResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_recipe_can_be_created_without_product()
    {
        $user = User::factory()->create([
            'is_manager' => true, // Only manager can create
            'is_active' => true,
        ]);

        $recipe = Recipe::create([
            'name' => 'Standalone Recipe',
            'product_id' => null,
            'description' => 'No product linked',
        ]);

        $this->assertDatabaseHas('recipes', [
            'name' => 'Standalone Recipe',
            'product_id' => null,
        ]);

        $this->actingAs($user)
            ->get(RecipeResource::getUrl('view', ['record' => $recipe]))
            ->assertSuccessful();
    }
}
