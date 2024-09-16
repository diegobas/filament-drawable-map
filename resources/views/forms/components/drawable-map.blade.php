<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>

    @php
        $affixLabelClasses = [
            'whitespace-nowrap group-focus-within:text-primary-500',
            'text-gray-400' => ! $errors->has($getStatePath()),
            'text-danger-400' => $errors->has($getStatePath()),
        ];

        $extraAlpineAttributes = $getExtraAlpineAttributes();
        $id = $getId();
        $isDisabled = $isDisabled();
        $isPrefixInline = $isPrefixInline();
        $isSuffixInline = $isSuffixInline();
        $prefixActions = $getPrefixActions();
        $prefixIcon = $getPrefixIcon();
        $prefixLabel = $getPrefixLabel();
        $suffixActions = $getSuffixActions();
        $suffixIcon = $getSuffixIcon();
        $suffixLabel = $getSuffixLabel();
        $statePath = $getStatePath();
        $location = $getLocation();
        $zoom = $getZoom();
        $color = $getColor();
        $clearButtonLabel = $getClearButtonLabel();
    @endphp

    <div
        id="filament-map-plugin-id-{{  $id }}"
        wire:ignore
        x-ignore
        ax-load
        ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('filament-drawable-map-component', 'diegobas/filament-drawable-map') }}"
        x-data="filamentDrawableMapPlugin({
            state: $wire.entangle('{{ $statePath }}'),
            color: @js($color),
            location: @js($location),
            zoom: @js($zoom),
            disabled: @js($isDisabled),
            clearButtonLabel: @js($clearButtonLabel)
        })"
    >

        <div class="flex-1">
            <div id="filament-drawable-map-container">
                <div id="filament-drawable-map-map" style="height:400px;"
                    class="border border-gray-300 block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-600 focus:ring-1 focus:ring-inset focus:ring-primary-600 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-600 border-gray-300 dark:border-gray-600">
                </div>
            </div>
        </div>

    </div>

</x-dynamic-component>
