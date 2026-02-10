<?php

namespace Tests\Feature;

use App\Livewire\ScanVoucher;
use App\Models\Event;
use App\Models\EventClaim;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ScanVoucherTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_scan_voucher_and_confirm()
    {
        $user = User::factory()->create();
        $voucher = Voucher::create([
            'code' => 'VOUCHER123',
            'value' => 500,
        ]);

        $component = Livewire::actingAs($user)
            ->test(ScanVoucher::class)
            // Need to set manualCode before calling checkCode if we simulate form submission?
            // No, calling the method directly should work, BUT
            // if checkCode relies on $this->manualCode being set via wire:model,
            // calling checkCode($arg) works IF the method accepts an arg.
            // My updated method accepts $code.
            ->call('checkCode', 'VOUCHER123')
            ->assertSet('scannedType', 'voucher')
            ->assertSet('scannedId', $voucher->id)
            ->assertSee('Hodnotový Voucher')
            ->assertHasNoErrors();

        // Not redeemed yet
        $this->assertNull($voucher->fresh()->used_at);

        // Confirm
        $component->call('confirmRedemption')
            ->assertSee('úspěšně uplatněn');

        $this->assertNotNull($voucher->fresh()->used_at);
        $this->assertEquals($user->id, $voucher->fresh()->used_by_user_id);
    }

    public function test_can_scan_event_claim_qr_token_and_confirm()
    {
        $event = Event::create([
            'title' => 'Test Event',
            'slug' => 'test-event-1',
            'start_at' => now(),
            'end_at' => now()->addHour()
        ]);
        $claim = EventClaim::create([
            'event_id' => $event->id,
            'claim_token' => 'long_token_string',
            'code' => 'SHORT1',
            'email' => 'test@example.com',
            'phone' => '123456789',
        ]);

        $component = Livewire::test(ScanVoucher::class)
            ->call('checkCode', 'long_token_string')
            ->assertSet('scannedType', 'claim')
            ->assertSet('scannedId', $claim->id)
            ->assertSee('Test Event')
            ->assertHasNoErrors();

        // Not redeemed yet
        $this->assertNull($claim->fresh()->redeemed_at);

        // Confirm with note
        $component->set('staffNote', 'VIP Customer')
            ->call('confirmRedemption')
            ->assertSee('úspěšně uplatněn');

        $this->assertNotNull($claim->fresh()->redeemed_at);
        $this->assertEquals('VIP Customer', $claim->fresh()->staff_note);
    }

    public function test_can_scan_event_claim_manual_code_and_confirm()
    {
        $event = Event::create([
            'title' => 'Test Event 2',
            'slug' => 'test-event-2',
            'start_at' => now(),
            'end_at' => now()->addHour()
        ]);
        $claim = EventClaim::create([
            'event_id' => $event->id,
            'claim_token' => 'another_token',
            'code' => 'ABCD',
            'email' => 'manual@example.com',
            'phone' => '987654321',
        ]);

        $component = Livewire::test(ScanVoucher::class)
            // When user types in input, manualCode is updated (deferred).
            // When form submits, it calls checkCode(manualCode).
            // So we should simulate that.
            ->set('manualCode', 'ABCD')
            ->call('checkCode', 'ABCD')
            ->assertSet('scannedType', 'claim')
            ->assertSet('scannedId', $claim->id)
            ->assertSee('Test Event 2')
            ->assertHasNoErrors();

        // Confirm
        $component->call('confirmRedemption');

        $this->assertNotNull($claim->fresh()->redeemed_at);
    }

    public function test_cannot_redeem_twice()
    {
        $event = Event::create([
            'title' => 'Test Event 3',
            'slug' => 'test-event-3',
            'start_at' => now(),
            'end_at' => now()->addHour()
        ]);
        $claim = EventClaim::create([
            'event_id' => $event->id,
            'claim_token' => 'token',
            'code' => 'CODE',
            'email' => 'twice@example.com',
            'phone' => '111222333',
            'redeemed_at' => now()->subMinute(),
        ]);

        Livewire::test(ScanVoucher::class)
            ->call('checkCode', 'CODE')
            ->assertHasErrors(['manualCode'])
            ->assertSee('už uplatněna');
    }
}
