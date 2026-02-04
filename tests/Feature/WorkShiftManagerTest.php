<?php

namespace Tests\Feature;

use App\Filament\Resources\WorkShiftResource;
use App\Models\User;
use App\Models\WorkShift;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WorkShiftManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_approve_shift_with_financials()
    {
        $manager = User::factory()->create(['is_manager' => true, 'name' => 'Manager']);
        $user = User::factory()->create([
            'name' => 'Employee',
            'hourly_rate' => 125,
            'salary_type' => 'hourly',
        ]);

        $shift = WorkShift::create([
            'user_id' => $user->id,
            'start_at' => now()->subHours(8),
            'end_at' => now(),
            'status' => 'pending_approval',
            // calculated_wage will be recalculated on save, so we rely on user rate * hours
        ]);

        Livewire::actingAs($manager)
            ->test(WorkShiftResource\Pages\ListWorkShifts::class)
            ->callTableAction('approve', $shift, data: [
                'advance_amount' => 200,
                'bonus' => 100,
                'bonus_note' => 'Great job',
                'penalty' => 50,
                'penalty_note' => 'Late',
            ])
            ->assertHasNoErrors();

        $shift->refresh();
        $this->assertEquals('approved', $shift->status);
        $this->assertEquals(200, $shift->advance_amount);
        $this->assertEquals(100, $shift->bonus);
        $this->assertEquals('Great job', $shift->bonus_note);
        $this->assertEquals(50, $shift->penalty);
        $this->assertEquals('Late', $shift->penalty_note);

        // Final payout check: 1000 + 100 - 50 - 200 = 850
        $this->assertEquals(850, $shift->final_payout);
    }

    public function test_manager_can_mark_paid()
    {
        $manager = User::factory()->create(['is_manager' => true]);
        $user = User::factory()->create();

        $shift = WorkShift::create([
            'user_id' => $user->id,
            'start_at' => now()->subHours(8),
            'end_at' => now(),
            'status' => 'approved',
            'calculated_wage' => 1000,
        ]);

        Livewire::actingAs($manager)
            ->test(WorkShiftResource\Pages\ListWorkShifts::class)
            ->callTableAction('mark_paid', $shift, data: [
                'payment_method' => 'cash',
            ])
            ->assertHasNoErrors();

        $shift->refresh();
        $this->assertEquals('paid', $shift->status);
        $this->assertEquals('cash', $shift->payment_method);
    }

    public function test_non_manager_cannot_approve()
    {
        $user = User::factory()->create(['is_manager' => false]);
        $shift = WorkShift::create([
            'user_id' => $user->id,
            'start_at' => now()->subHours(8),
            'end_at' => now(),
            'status' => 'pending_approval',
        ]);

        Livewire::actingAs($user)
            ->test(WorkShiftResource\Pages\ListWorkShifts::class)
            ->assertTableActionHidden('approve', $shift);
    }
}
