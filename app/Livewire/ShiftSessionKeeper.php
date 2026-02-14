<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkShift;

class ShiftSessionKeeper extends Component
{
    public $lastNotificationId;

    public function mount()
    {
        $user = Auth::user();
        if ($user) {
            $this->lastNotificationId = $user->unreadNotifications()->latest()->first()?->id;
        }
    }

    public function keepAlive()
    {
        // Session extended automatically by this request
        $this->checkNotifications();
    }

    public function checkNotifications()
    {
        $user = Auth::user();
        if (!$user) return;

        $latest = $user->unreadNotifications()->latest()->first();

        if ($latest && $latest->id !== $this->lastNotificationId) {
            $this->lastNotificationId = $latest->id;

            // Send browser event
            $this->dispatch('browser-notification',
                title: $latest->data['title'] ?? 'Nová notifikace',
                body: $latest->data['body'] ?? ''
            );
        }
    }

    public function render()
    {
        $shouldPoll = false;

        if (Auth::check()) {
            $user = Auth::user();

            // Optimization: Cache this if performance issue arises
            $hasActiveShift = WorkShift::where('user_id', $user->id)
                ->whereNull('end_at')
                ->exists();

            if ($user->keep_logged_in_during_shift && $hasActiveShift) {
                $shouldPoll = true;
            }
        }

        return view('livewire.shift-session-keeper', [
            'shouldPoll' => $shouldPoll
        ]);
    }
}
