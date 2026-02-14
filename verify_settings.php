<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';

$app = require __DIR__.'/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

try {
    $settings = app(\App\Settings\StoryousSettings::class);
    echo "StoryousSettings loaded successfully.\n";
    echo "sync_start_date: " . ($settings->sync_start_date ?? 'NULL') . "\n";
} catch (\Exception $e) {
    echo "Failed to load settings: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
