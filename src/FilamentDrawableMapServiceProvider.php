<?php

namespace DiegoBas\FilamentDrawableMap;

use Filament\Facades\Filament;
use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;

class FilamentDrawableMapServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-drawable-map';

    protected array $resources = [
        // CustomResource::class,
    ];

    protected array $pages = [
        // CustomPage::class,
    ];

    protected array $widgets = [
        // CustomWidget::class,
    ];

    protected array $styles = [
        'plugin-filament-drawable-map' => __DIR__.'/../resources/dist/filament-drawable-map.css',
    ];

    protected array $scripts = [
        'plugin-filament-drawable-map' => __DIR__.'/../resources/dist/filament-drawable-map.js',
    ];

    // protected array $beforeCoreScripts = [
    //     'plugin-filament-drawable-map' => __DIR__ . '/../resources/dist/filament-drawable-map.js',
    // ];

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasTranslations()
            ->hasConfigFile()
            ->hasAssets()
            ->hasViews();
    }
}
