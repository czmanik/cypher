<?php

namespace Tests\Feature;

use App\Filament\Widgets\CurrentShiftWidget;
use App\Models\ChecklistTemplate;
use App\Models\PlannedShift;
use App\Models\User;
use App\Models\WorkShift;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WorkShiftTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_start_shift()
    {
        $user = User::factory()->create(['employee_type' => 'kitchen']);

        Livewire::actingAs($user)
            ->test(CurrentShiftWidget::class)
            ->callAction('startShift')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('work_shifts', [
            'user_id' => $user->id,
            'status' => 'active',
        ]);
    }

    public function test_start_shift_links_planned_shift()
    {
        $user = User::factory()->create(['employee_type' => 'floor']);
        $planned = PlannedShift::create([
            'user_id' => $user->id,
            'start_at' => now()->subHour(),
            'end_at' => now()->addHour(),
            'is_published' => true,
        ]);

        Livewire::actingAs($user)
            ->test(CurrentShiftWidget::class)
            ->callAction('startShift')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('work_shifts', [
            'user_id' => $user->id,
            'planned_shift_id' => $planned->id,
            'status' => 'active',
        ]);
    }

    public function test_employee_can_end_shift_with_checklist()
    {
        $user = User::factory()->create(['employee_type' => 'kitchen']);
        $shift = WorkShift::create([
            'user_id' => $user->id,
            'start_at' => now()->subHours(8),
            'status' => 'active',
        ]);

        $template = ChecklistTemplate::create([
            'task_name' => 'Clean Kitchen',
            'is_required' => true,
            'target_type' => 'all',
        ]);

        Livewire::actingAs($user)
            ->test(CurrentShiftWidget::class)
            ->callAction('endShift', data: [
                'tasks' => [
                    $template->id => [
                        'template_id' => $template->id,
                        'task_name' => 'Clean Kitchen',
                        'is_completed' => true,
                        'note' => 'Done',
                    ]
                ]
            ])
            ->assertHasNoErrors();

        $this->assertNotNull($shift->fresh()->end_at);
        $this->assertEquals('pending_approval', $shift->fresh()->status);

        $this->assertDatabaseHas('shift_checklist_results', [
            'work_shift_id' => $shift->id,
            'task_name' => 'Clean Kitchen',
            'is_completed' => true,
            'note' => 'Done',
        ]);
    }
}
