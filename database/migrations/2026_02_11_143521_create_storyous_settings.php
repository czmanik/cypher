<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('storyous.client_id', null);
        $this->migrator->add('storyous.client_secret', null);
        $this->migrator->add('storyous.merchant_id', null);
        $this->migrator->add('storyous.place_id', null);
    }
};
