<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SeoSettings extends Settings
{
    public string $site_name;
    public ?string $site_description;
    public ?string $site_image;
    public string $robots_default;
    public string $title_separator;
    public ?string $title_suffix;

    public static function group(): string
    {
        return 'seo';
    }
}
