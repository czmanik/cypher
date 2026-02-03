<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('seo.site_name', 'Cypher93');
        $this->migrator->add('seo.site_description', 'Cypher93 - Bar & Event Space');
        $this->migrator->add('seo.site_image', null);
        $this->migrator->add('seo.robots_default', 'index, follow');
        $this->migrator->add('seo.title_separator', '|');
        $this->migrator->add('seo.title_suffix', 'Cypher93');
    }
};
