<?php

namespace DiegoBas\FilamentDrawableMap\Forms\Components;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Contracts;
use Filament\Forms\Components\Concerns;
use Filament\Notifications\Notification;
use Filament\Support\Concerns\HasExtraAlpineAttributes;

class DrawableMap extends Field implements Contracts\CanBeLengthConstrained
{
    use Concerns\CanBeAutocapitalized;
    use Concerns\CanBeLengthConstrained;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasAffixes;
    use Concerns\HasPlaceholder;
    use HasExtraAlpineAttributes;

    protected string $view = 'filament-drawable-map::forms.components.drawable-map';

    public $mode = 'light';

    public function setMode($value): static
    {
        $this->mode = $value;

        return $this;
    }

    public function getMode()
    {
        return $this->mode;
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

    public function callBeforeStateDehydrated(): static
    {
        return parent::callBeforeStateDehydrated();
    }

    public function getStateToDehydrate(): array
    {
        $state = parent::getStateToDehydrate();
        $key = array_key_first($state);
        $value = $state[$key];

        if (is_string($value)) {
            $value = json_decode($value, true);
            $state[$key] = $value;
        }

        return $state;
    }
}
