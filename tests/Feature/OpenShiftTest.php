<?php

namespace Tests\Feature;

use App\Models\PlannedShift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpenShiftTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_create_open_shift()
    {
        $manager = User::factory()->create([
            'is_manager' => true,
            'is_active' => true, // Ensure active
        ]);

        $this->actingAs($manager);

        $shift = PlannedShift::create([
            'user_id' => null,
            'start_at' => now()->addDay()->setTime(9,0),
            'end_at' => now()->addDay()->setTime(17,0),
            'status' => 'pending',
            'is_published' => true,
            'bonus' => 500,
        ]);

        $this->assertDatabaseHas('planned_shifts', [
            'id' => $shift->id,
            'user_id' => null,
            'bonus' => 500,
        ]);
    }

    public function test_employee_can_see_open_shifts()
    {
        $user = User::factory()->create([
            'employee_type' => 'kitchen',
            'is_active' => true, // Ensure active for Filament access
        ]);

        $shift = PlannedShift::create([
            'user_id' => null,
            'start_at' => now()->addDay(),
            'end_at' => now()->addDay()->addHours(8),
            'shift_role' => 'kitchen',
            'is_published' => true,
        ]);

        $this->actingAs($user)
            ->get(route('filament.admin.pages.open-shift-market'))
            ->assertStatus(200)
            ->assertSee('Tržiště směn');
    }
}
