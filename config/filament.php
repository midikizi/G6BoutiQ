<?php

return [
    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),

    'layout' => [
        'sidebar' => [
            'is_collapsible_on_desktop' => true,
        ],
        'footer' => [
            'should_show_logo' => false,
        ],
    ],

    'favicon' => null,

    'auth' => [
        'guard' => env('FILAMENT_AUTH_GUARD', 'web'),
        'pages' => [
            'login' => \Filament\Pages\Auth\Login::class,
        ],
    ],

    'pages' => [
        'namespace' => 'App\\Filament\\Pages',
        'path' => app_path('Filament/Pages'),
        'register' => [],
    ],

    'resources' => [
        'namespace' => 'App\\Filament\\Resources',
        'path' => app_path('Filament/Resources'),
        'register' => [],
    ],

    'widgets' => [
        'namespace' => 'App\\Filament\\Widgets',
        'path' => app_path('Filament/Widgets'),
        'register' => [],
    ],

    'home_url' => '/admin/login',

    'domain' => env('FILAMENT_DOMAIN'),
    'path' => env('FILAMENT_PATH', 'admin'),
    'brand' => env('APP_NAME'),

    'colors' => [
        'primary' => \Filament\Support\Colors\Color::Amber,
    ],

    'default_avatar_provider' => \Filament\AvatarProviders\UiAvatarsProvider::class,
]; 