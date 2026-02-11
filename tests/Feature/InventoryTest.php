<?php

namespace Tests\Feature;

use App\Filament\Pages\FastWriteOff;
use App\Filament\Resources\InventoryItemResource\Pages\ListInventoryItems;
use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_action()
    {
        $manager = User::factory()->create(['is_manager' => true, 'is_active' => true]);
        $item = InventoryItem::create([
            'name' => 'Test Item',
            'category' => 'ingredient',
            'stock_qty' => 10,
            'unit' => 'kg',
            'package_size' => 5,
            'price' => 100,
        ]);

        $this->actingAs($manager);

        Livewire::test(ListInventoryItems::class)
            ->mountAction('purchase')
            ->setActionData([
                'items' => [
                    'uuid1' => [
                        'inventory_item_id' => $item->id,
                        'quantity' => 2,
                        'price' => 120,
                    ]
                ]
            ])
            ->callMountedAction()
            ->assertHasNoActionErrors()
            ->assertNotified();

        $item->refresh();
        $this->assertEquals(20, $item->stock_qty); // 10 + (2*5)
        $this->assertEquals(120, $item->price);
    }

    public function test_fast_write_off_submit()
    {
        $staff = User::factory()->create(['is_active' => true]);
        $item = InventoryItem::create([
            'name' => 'Milk',
            'category' => 'ingredient',
            'stock_qty' => 10,
            'unit' => 'l',
            'package_size' => 1,
            'price' => 20,
        ]);

        $this->actingAs($staff);

        Livewire::test(FastWriteOff::class)
            ->call('toggleItem', $item->id)
            ->call('toggleItem', $item->id)
            ->call('submit')
            ->assertNotified();

        $item->refresh();
        $this->assertEquals(8, $item->stock_qty);
    }

    public function test_staff_cannot_access_inventory_resource()
    {
        $staff = User::factory()->create(['is_manager' => false, 'is_active' => true]);

        $this->actingAs($staff);

        $response = $this->get(ListInventoryItems::getUrl());
        $response->assertForbidden();
    }

    public function test_inactive_user_cannot_access_fast_write_off()
    {
        $inactive = User::factory()->create(['is_active' => false]);

        $this->actingAs($inactive);

        $response = $this->get(FastWriteOff::getUrl());
        $response->assertForbidden();
    }

}
