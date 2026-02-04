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

    public function test_manager_can_render_recipe_resource_page()
    {
        $user = User::factory()->create([
            'is_manager' => true,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get(RecipeResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_manager_can_list_recipes_with_roles()
    {
        $user = User::factory()->create([
            'is_manager' => true,
            'is_active' => true,
        ]);

        $category = Category::create(['name' => 'Food', 'slug' => 'food', 'is_active' => true]);
        $product = Product::create([
            'name' => 'Test Product',
            'category_id' => $category->id,
            'price' => 100,
            'is_available' => true,
        ]);

        Recipe::create([
            'product_id' => $product->id,
            'allowed_roles' => [User::TYPE_KITCHEN, User::TYPE_FLOOR],
            'description' => 'Test',
        ]);

        // This should fail if the column logic is wrong
        $this->actingAs($user)
            ->get(RecipeResource::getUrl('index'))
            ->assertSuccessful();
    }
}
