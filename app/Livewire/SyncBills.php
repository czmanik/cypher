<?php

namespace App\Livewire;

use App\Services\StoryousService;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Livewire\Component;

class SyncBills extends Component
{
    public $fromDate;
    public $toDate;

    // Internal state
    public $currentDate;
    public $isProcessing = false;
    public $isFinished = false;

    public $stats = [
        'processed_days' => 0,
        'bills_created' => 0,
        'bills_updated' => 0,
        'errors' => 0,
    ];

    public $logs = [];

    public function mount()
    {
        // Default range: from stored settings or 1 month ago -> now
        $settings = app(\App\Settings\StoryousSettings::class);
        $this->fromDate = $settings->sync_start_date ?? now()->subMonth()->format('Y-m-d');
        $this->toDate = now()->format('Y-m-d');
    }

    public function startSync()
    {
        $this->isProcessing = true;
        $this->isFinished = false;
        $this->currentDate = $this->fromDate;
        $this->stats = [
            'processed_days' => 0,
            'bills_created' => 0,
            'bills_updated' => 0,
            'errors' => 0,
        ];
        $this->logs = [];

        $this->logs[] = "Starting sync from {$this->fromDate} to {$this->toDate}...";
    }

    public function processNextDay(StoryousService $service)
    {
        if (!$this->isProcessing) return;

        $current = Carbon::parse($this->currentDate);
        $end = Carbon::parse($this->toDate);

        if ($current->gt($end)) {
            $this->isProcessing = false;
            $this->isFinished = true;
            $this->logs[] = "Sync completed!";
            Notification::make()->title('Synchronizace dokončena')->success()->send();
            return;
        }

        // Process this single day by passing same start/end date
        $result = $service->syncBills($current, $current);

        if ($result['status'] === 'success') {
            $dayStats = $result['stats'];
            $this->stats['processed_days']++;
            $this->stats['bills_created'] += $dayStats['bills_created'];
            $this->stats['bills_updated'] += $dayStats['bills_updated'];

            $msg = "{$current->toDateString()}: +{$dayStats['bills_created']} new, ~{$dayStats['bills_updated']} updated.";
            if ($dayStats['errors'] > 0) {
                $msg .= " ({$dayStats['errors']} errors)";
                $this->stats['errors'] += $dayStats['errors'];
            }
            // Add to beginning of log
            array_unshift($this->logs, $msg);
        } else {
            $this->stats['errors']++;
            array_unshift($this->logs, "Error {$current->toDateString()}: " . ($result['message'] ?? 'Unknown'));
        }

        // Move to next day
        $this->currentDate = $current->addDay()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.sync-bills');
    }
}
