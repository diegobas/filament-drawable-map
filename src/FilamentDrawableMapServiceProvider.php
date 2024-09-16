<?php

namespace DiegoBas\FilamentDrawableMap;

use Filament\Facades\Filament;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentDrawableMapServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-drawable-map';
    public static string $viewNamespace = 'filament-drawable-map';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name);

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void
    {
    }

    public function packageBooted(): void
    {
        FilamentAsset::registerScriptData([
            'api' => [
                'key' => env('GOOGLE_MAPS_API_KEY'),
            ],
            'location' => [
                'latitude' => config('filament-map-plugin.location.latitude'),
                'longitude' => config('filament-map-plugin.location.longitude'),
            ],
        ]);

        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );
    }

    protected function getAssetPackageName(): ?string
    {
        return 'diegobas/filament-drawable-map';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            AlpineComponent::make('filament-drawable-map-component', __DIR__ . '/../resources/dist/filament-drawable-map.js'),
            Css::make('filament-drawable-map-styles', __DIR__ . '/../resources/dist/filament-drawable-map.css')
        ];
    }
}
