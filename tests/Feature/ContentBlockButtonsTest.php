<?php

namespace Tests\Feature;

use App\Models\ContentBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentBlockButtonsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_store_and_retrieve_buttons()
    {
        $buttons = [
            [
                'label' => 'Test Button',
                'url' => 'https://example.com',
                'style' => 'primary',
            ],
            [
                'label' => 'Secondary Button',
                'url' => '/test',
                'style' => 'secondary',
            ]
        ];

        $block = ContentBlock::create([
            'key' => 'homepage_hero',
            'title' => 'Hero Title',
            'buttons' => $buttons,
        ]);

        $this->assertDatabaseHas('content_blocks', [
            'key' => 'homepage_hero',
        ]);

        $retrieved = ContentBlock::find($block->id);

        $this->assertIsArray($retrieved->buttons);
        $this->assertCount(2, $retrieved->buttons);
        $this->assertEquals('Test Button', $retrieved->buttons[0]['label']);
    }
}
