<?php

use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('manager can access page resource', function () {
    $user = User::factory()->create(['is_manager' => true]);

    actingAs($user)
        ->get(App\Filament\Resources\PageResource::getUrl())
        ->assertStatus(200);
});

test('non manager cannot access page resource', function () {
    $user = User::factory()->create(['is_manager' => false]);

    actingAs($user)
        ->get(App\Filament\Resources\PageResource::getUrl())
        ->assertForbidden();
});

test('manager can access global seo settings', function () {
    $user = User::factory()->create(['is_manager' => true]);

    actingAs($user)
        ->get(App\Filament\Pages\ManageSeo::getUrl())
        ->assertStatus(200);
});

test('non manager cannot access global seo settings', function () {
    $user = User::factory()->create(['is_manager' => false]);

    actingAs($user)
        ->get(App\Filament\Pages\ManageSeo::getUrl())
        ->assertForbidden();
});
