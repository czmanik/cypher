<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('footer.measuring_code', null);
        $this->migrator->add('footer.copyright_text', '© 2024 Cypher93. Všechna práva vyhrazena.');

        // Left Column (Text)
        $this->migrator->add('footer.column_left_type', 'text');
        $this->migrator->add('footer.column_left_text', '<p><strong>Adresa:</strong><br>Koněvova, Praha 3 - Žižkov</p>');

        // Center Column (Opening Hours)
        $this->migrator->add('footer.column_center_type', 'opening_hours');
        $this->migrator->add('footer.column_center_text', null);

        // Right Column (Socials)
        $this->migrator->add('footer.column_right_type', 'socials');
        $this->migrator->add('footer.column_right_text', null);

        // Social Links
        $this->migrator->add('footer.social_links', []);
    }
};
