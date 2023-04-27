<?php

namespace DiegoBas\FilamentDrawableMap\Forms\Components;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Contracts;
use Filament\Forms\Components\Concerns;
use Filament\Notifications\Notification;
use Filament\Support\Concerns\HasExtraAlpineAttributes;

class ViewableMap extends Field implements Contracts\CanBeLengthConstrained
{
    use Concerns\CanBeAutocapitalized;
    use Concerns\CanBeLengthConstrained;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasAffixes;
    use Concerns\HasPlaceholder;
    use HasExtraAlpineAttributes;

    protected string $view = 'filament-drawable-map::forms.components.viewable-map';

    protected $titles;
    protected $polygons;
    protected $color;

    public $mode = 'light';


    protected function setUp(): void
    {
        parent::setUp();

        $this->default([
            'polygon' => null
        ]);
    }

    public function titles(array | Closure | null $titles): static
    {
        $this->titles = $titles;

        return $this;
    }

    public function getTitles()
    {
        $titles = $this->evaluate($this->titles);

        return $titles;
    }

    public function color(Closure | string | null $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getColor(): string | null
    {
        $color = $this->evaluate($this->color);

        return $color;
    }

    public function mode($value): static
    {
        $this->mode = $value;

        return $this;
    }

    public function getMode()
    {
        return $this->mode;
    }

    public function polygons(array | Closure | null $polygons): static
    {
        $this->polygons = $polygons;

        return $this;
    }

    public function getPolygons()
    {
        $polygons = $this->evaluate($this->polygons);

        return $polygons;
    }

    public function getState()
    {
        $state = parent::getState();

        if (is_array($state)) {
            return json_encode($state);
        }

        return $state;
    }

    public function callAfterStateHydrated(): static
    {
        $state = $this->getState();
        if (is_string($state)) {
            $state = json_decode($state, true);
            $this->state($state);
        }
        return parent::callAfterStateHydrated();
    }
}
