<?php

namespace Tests\Unit;

use App\Models\InventoryItem;
use PHPUnit\Framework\TestCase;

class InventoryItemTest extends TestCase
{
    public function test_stock_status_logic(): void
    {
        $item = new InventoryItem();
        $item->min_stock_qty = 10;

        // Test Critical
        $item->stock_qty = 0;
        $this->assertEquals('critical', $item->stock_status);

        $item->stock_qty = -1;
        $this->assertEquals('critical', $item->stock_status);

        // Test Low (Orange)
        $item->stock_qty = 5; // < 10
        $this->assertEquals('low', $item->stock_status);

        $item->stock_qty = 10; // == 10
        $this->assertEquals('low', $item->stock_status);

        // Test OK (Green)
        $item->stock_qty = 11;
        $this->assertEquals('ok', $item->stock_status);
    }
}
