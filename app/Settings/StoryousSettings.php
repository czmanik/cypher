<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class StoryousSettings extends Settings
{
    public ?string $client_id;
    public ?string $client_secret;
    public ?string $merchant_id;
    public ?string $api_key; // In case they use simple API key

    public static function group(): string
    {
        return 'storyous';
    }
}
