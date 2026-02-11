<?php

return [

    /*
     * Each settings class used in your application must be registered here.
     */
    'settings' => [
        App\Settings\SeoSettings::class,
        App\Settings\FooterSettings::class,
        App\Settings\StoryousSettings::class,
    ],

    /*
     * The path where the settings classes will be created.
     */
    'setting_class_path' => app_path('Settings'),

    /*
     * In these directories settings migrations will be stored and ran when migrating. A settings
     * migration created via the make:settings-migration command will be stored in the first path or
     * a custom defined path when running the command.
     */
    'migrations_paths' => [
        database_path('settings'),
    ],

    /*
     * The table used in the database to store the settings.
     */
    'table' => 'settings',

    /*
     * The encoder used to encode and decode values.
     */
    'encoder' => null,

    /*
     * The cache store used to cache settings.
     */
    'cache' => [
        'store' => null,
        'prefix' => null,
    ],

    /*
     * These columns will be used to load the settings from the database.
     */
    'load_columns' => [
        'group',
        'name',
        'locked',
        'payload',
    ],

    /*
     * The repository used to load the settings from the database.
     */
    'repository' => Spatie\LaravelSettings\SettingsRepositories\DatabaseSettingsRepository::class,

    /*
     * The contents of the settings classes can be cached.
     */
    'cache_classes' => true,

    /*
     * The transformer used to transform the settings.
     */
    'transformer' => null,
];
