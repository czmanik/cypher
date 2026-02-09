<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class FooterSettings extends Settings
{
    public ?string $measuring_code;
    public string $copyright_text;

    // Layout configuration: 'text', 'opening_hours', 'socials', 'none'
    public string $column_left_type;
    public ?string $column_left_text;

    public string $column_center_type;
    public ?string $column_center_text;

    public string $column_right_type;
    public ?string $column_right_text;

    public array $social_links;

    public static function group(): string
    {
        return 'footer';
    }
}
