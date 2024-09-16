<?php

namespace DiegoBas\FilamentDrawableMap\Traits;

trait InteractsWithMap
{
    private array $location = [
        'latitude' => 0,
        'longitude' => 0,
    ];

    private int $zoom = 13;
    private ?string $clearButtonLabel = 'Clear';

    public function getLocation(): array
    {
        return $this->location;
    }

    public function location(array $location): static
    {

        $this->location = array_intersect_key($location, array_flip(['latitude', 'longitude']));

        return $this;
    }

    public function getZoom(): int
    {
        return $this->zoom;
    }

    public function zoom(int $zoom): static
    {
        $this->zoom = $zoom;

        return $this;
    }

    public function getClearButtonLabel(): ?string
    {
        return $this->clearButtonLabel;
    }

    public function clearButtonLabel(string $label): static
    {
        $this->clearButtonLabel = $label;

        return $this;
    }
}
