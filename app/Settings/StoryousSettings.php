<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class StoryousSettings extends Settings
{
    public ?string $client_id;
    public ?string $client_secret;
    public ?string $merchant_id;
    public ?string $place_id;
    public ?string $sync_start_date;

    public static function group(): string
    {
        return 'storyous';
    }
}
