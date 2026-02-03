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

    public function test_can_scan_voucher()
    {
        $user = User::factory()->create();
        $voucher = Voucher::create([
            'code' => 'VOUCHER123',
            'value' => 500,
        ]);

        Livewire::actingAs($user)
            ->test(ScanVoucher::class)
            ->call('checkCode', 'VOUCHER123')
            ->assertSee('úspěšně uplatněn')
            ->assertHasNoErrors();

        $this->assertNotNull($voucher->fresh()->used_at);
        $this->assertEquals($user->id, $voucher->fresh()->used_by_user_id);
    }

    public function test_can_scan_event_claim_qr_token()
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
        ]);

        Livewire::test(ScanVoucher::class)
            ->call('checkCode', 'long_token_string')
            ->assertSee('úspěšně uplatněn')
            ->assertHasNoErrors();

        $this->assertNotNull($claim->fresh()->redeemed_at);
    }

    public function test_can_scan_event_claim_manual_code()
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
            'code' => 'ABCDEF',
        ]);

        Livewire::test(ScanVoucher::class)
            ->call('checkCode', 'ABCDEF')
            ->assertSee('úspěšně uplatněn')
            ->assertHasNoErrors();

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
            'redeemed_at' => now()->subMinute(),
        ]);

        Livewire::test(ScanVoucher::class)
            ->call('checkCode', 'CODE')
            ->assertSee('už uplatněna');
    }
}
