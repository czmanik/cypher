<?php

use App\Filament\Resources\OpeningHourResource;
use App\Models\OpeningHour;
use App\Models\User;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('can create opening hour', function () {
    // Create a manager user
    $user = User::factory()->create([
        'is_manager' => true,
    ]);

    $this->actingAs($user);

    Livewire::test(OpeningHourResource\Pages\CreateOpeningHour::class)
        ->fillForm([
            'day_of_week' => '1',
            'bar_open' => '10:00',
            'bar_close' => '22:00',
            'kitchen_open' => '11:00',
            'kitchen_close' => '21:00',
        ])
        ->call('create')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('opening_hours', [
        'day_of_week' => '1',
        'bar_open' => '10:00',
    ]);
});

it('prevents duplicate days', function () {
    $user = User::factory()->create(['is_manager' => true]);
    $this->actingAs($user);

    OpeningHour::create([
        'day_of_week' => '1',
        'is_closed' => true,
    ]);

    Livewire::test(OpeningHourResource\Pages\CreateOpeningHour::class)
        ->fillForm([
            'day_of_week' => '1',
            'is_closed' => true,
        ])
        ->call('create')
        ->assertHasErrors(['data.day_of_week']);
});

it('cannot edit day of week', function () {
    $user = User::factory()->create(['is_manager' => true]);
    $this->actingAs($user);

    $openingHour = OpeningHour::create([
        'day_of_week' => '2',
        'is_closed' => true,
    ]);

    Livewire::test(OpeningHourResource\Pages\EditOpeningHour::class, [
        'record' => $openingHour->getKey(),
    ])
    ->assertFormFieldIsDisabled('day_of_week');
});
