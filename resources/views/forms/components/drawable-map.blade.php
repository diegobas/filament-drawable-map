@php
$affixLabelClasses = [
'whitespace-nowrap group-focus-within:text-primary-500',
'text-gray-400' => ! $errors->has($getStatePath()),
'text-danger-400' => $errors->has($getStatePath()),
];
@endphp

<x-forms::field-wrapper :id="$getId()" :label="$getLabel()" :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()" :hint="$getHint()" :required="$isRequired()" :state-path="$getStatePath()">

    <div x-data="filamentDrawableMap($wire.{{ $applyStateBindingModifiers('entangle(\'' . $getStatePath() . '\')') }})" {{
        $attributes->
        merge($getExtraAttributes())->class(['flex items-center space-x-2
        rtl:space-x-reverse group
        filament-forms-text-input-component']) }} wire:ignore id="{{ $getId() }}">

        @if (($prefixAction = $getPrefixAction()) && (! $prefixAction->isHidden()))
        {{ $prefixAction }}
        @endif

        @if ($icon = $getPrefixIcon())
        <x-dynamic-component :component="$icon" class="w-5 h-5" />
        @endif

        @if ($label = $getPrefixLabel())
        <span @class($affixLabelClasses)>
            {{ $label }}
        </span>
        @endif

        <div class="flex-1">
            <div id="filament-drawable-map-container">
                <div id="filament-drawable-map-map" style="height:400px;"
                    class="border border-gray-300 block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-600 focus:ring-1 focus:ring-inset focus:ring-primary-600 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-600 border-gray-300 dark:border-gray-600">
                </div>
            </div>
        </div>
    </div>

</x-forms::field-wrapper>

@push('scripts')
<script>
    // Create the script tag, set the appropriate attributes
    var script = document.createElement('script');
    script.src = "https://maps.googleapis.com/maps/api/js?key={{ config('filament-drawable-map.providers.google.key') }}&libraries=places&callback=initMap&language={{ App::getLocale() }}";
    script.type = 'text/javascript';
    script.async = true;

    var map;
    var marker;
    var fill;
    var mode = '{{ $getMode() }}';
    var data = null;

    // Attach your callback function to the `window` object
    function initMap() {
        var editable = false;
        var polygon = new google.maps.Polygon({
            strokeColor: "#FF0000",
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: "#FF0000",
            fillOpacity: 0.35,
            draggable: false,
            geodesic: true,
        });
        var location = {
            lat: {{ config('filament-drawable-map.location.latitude') }},
            lng: {{ config('filament-drawable-map.location.longitude') }}
        };

        var div = document.getElementById("filament-drawable-map-map");
        var darkMode = [
            { elementType: "geometry", stylers: [{ color: "#242f3e" }] },
            { elementType: "labels.text.stroke", stylers: [{ color: "#242f3e" }] },
            { elementType: "labels.text.fill", stylers: [{ color: "#746855" }] },
            {
                featureType: "administrative.locality",
                elementType: "labels.text.fill",
                stylers: [{ color: "#d59563" }],
            },
            {
                featureType: "poi",
                elementType: "labels.text.fill",
                stylers: [{ color: "#d59563" }],
            },
            {
                featureType: "poi.park",
                elementType: "geometry",
                stylers: [{ color: "#263c3f" }],
            },
            {
                featureType: "poi.park",
                elementType: "labels.text.fill",
                stylers: [{ color: "#6b9a76" }],
            },
            {
                featureType: "road",
                elementType: "geometry",
                stylers: [{ color: "#38414e" }],
            },
            {
                featureType: "road",
                elementType: "geometry.stroke",
                stylers: [{ color: "#212a37" }],
            },
            {
                featureType: "road",
                elementType: "labels.text.fill",
                stylers: [{ color: "#9ca5b3" }],
            },
            {
                featureType: "road.highway",
                elementType: "geometry",
                stylers: [{ color: "#746855" }],
            },
            {
                featureType: "road.highway",
                elementType: "geometry.stroke",
                stylers: [{ color: "#1f2835" }],
            },
            {
                featureType: "road.highway",
                elementType: "labels.text.fill",
                stylers: [{ color: "#f3d19c" }],
            },
            {
                featureType: "transit",
                elementType: "geometry",
                stylers: [{ color: "#2f3948" }],
            },
            {
                featureType: "transit.station",
                elementType: "labels.text.fill",
                stylers: [{ color: "#d59563" }],
            },
            {
                featureType: "water",
                elementType: "geometry",
                stylers: [{ color: "#17263c" }],
            },
            {
                featureType: "water",
                elementType: "labels.text.fill",
                stylers: [{ color: "#515c6d" }],
            },
            {
                featureType: "water",
                elementType: "labels.text.stroke",
                stylers: [{ color: "#17263c" }],
            },
        ];

        map = new google.maps.Map(div, {
            zoom: 15,
            center: location,
            disableDefaultUI: true,
            zoomControl: true,
            mapTypeControl: true,
            scaleControl: true,
            streetViewControl: false,
            rotateControl: false,
            fullscreenControl: true,
            tilt: 0,
            gestureHandling: "{!! $isDisabled() ? 'none' : 'cooperative' !!}",
            clickableIcons: {!! $isDisabled() ? 'false' : 'true' !!},
            @if ( $getMode() === 'dark' )
            styles: darkMode,
            @endif
        });

        const centerControlDiv = document.createElement("div");
        const centerControl = createClearControl(map);
        centerControl.disable();
        centerControlDiv.appendChild(centerControl);
        map.controls[google.maps.ControlPosition.TOP_CENTER].push(centerControlDiv);

        if (!google.maps.Polygon.prototype.getBounds) {
            google.maps.Polygon.prototype.getBounds = function() {
                var bounds = new google.maps.LatLngBounds()
                this.getPath().forEach(function(element,index){bounds.extend(element)})

                return bounds
            }
        }

       if (!google.maps.Map.prototype.clearZone) {
            google.maps.Map.prototype.clearZone = function() {
                if (polygon) {
                    polygon.setMap(null);
                    const path = polygon.getPath()
                    path.clear()
                    sync(polygon, path)
                }

                centerControl.disable();
            }
        }

        @if (!$isDisabled())
        google.maps.event.addListener(map, 'click', function(event) {
            if (event.domEvent.ctrlKey) {
                if (polygon) {
                    const path = polygon.getPath()
                    path.push(event.latLng)

                    if (!polygon.getMap()) {
                        polygon.setMap(map)
                    }
                }

                if (centerControl.disabled) {
                    centerControl.enable();
                }
            } else {
                map.panTo(event.latLng);
            }
        });

        google.maps.event.addDomListener(document, 'keyup', function (event) {
            if (event.key === "Control") {
                editable = false;
                if (polygon) { polygon.setEditable(editable); }
                map.setOptions({
                    draggableCursor:'',
                    draggingCursor: ''});
            }
        });

        google.maps.event.addDomListener(document, 'keydown', function (event) {
            if (event.key === "Control") {
                editable = true;
                if (polygon) { polygon.setEditable(editable); }
                map.setOptions({
                    draggableCursor:'crossHair',
                    draggingCursor: 'crossHair'});
            }
        });

        polygon.addListener('dblclick', function (event) {
            if (polygon.getEditable() && event.vertex !== undefined) {
                const path = polygon.getPath()
                path.removeAt(event.vertex)
            }
        })

        const path = polygon.getPath()
        path.addListener('insert_at', function (event) {
            sync(polygon, this)
        })
        path.addListener('remove_at', function (event) {
            sync(polygon, this)
        })
        path.addListener('set_at', function (event) {
            sync(polygon, this)
        })

        @else

        google.maps.event.clearListeners(map);
        google.maps.event.clearInstanceListeners(map);
        @endif

        if (data && data.state !== null){//} && data.state.polygon !== null) {
            const path = polygon.getPath()
            data.state.forEach((point, index) => {
                const latLng = new google.maps.LatLng(point.lat, point.lng)
                path.push(latLng)
            })
            var bounds = polygon.getBounds()
            if (bounds) {
                map.fitBounds(bounds)
                map.panToBounds(bounds)
            }

            centerControl.enable()
            polygon.setMap(map)
        }
    };

    function sync(zone, path) {
        if (zone && zone.getMap()) {
            data.state = path.getArray()
        } else {
            if (path.length === 0) {
                data.state = null
            }
        }
    }

    function createClearControl(map) {
        const controlButton = document.createElement("button");

        // Set CSS for the control.
        controlButton.style.backgroundColor = "#fff";
        controlButton.style.border = "2px solid #fff";
        controlButton.style.borderRadius = "3px";
        controlButton.style.boxShadow = "0 2px 6px rgba(0,0,0,.3)";
        controlButton.style.color = "rgb(25,25,25)";
        controlButton.style.cursor = "pointer";
        controlButton.style.fontFamily = "Roboto,Arial,sans-serif";
        controlButton.style.fontSize = "16px";
        controlButton.style.lineHeight = "38px";
        controlButton.style.margin = "8px 0 22px";
        controlButton.style.padding = "0 5px";
        controlButton.style.textAlign = "center";
        controlButton.textContent = "{{ trans('filament-drawable-map::filament-drawable-map.clear_zone') }}";
        controlButton.title = "{{ trans('filament-drawable-map::filament-drawable-map.clear_zone_description') }}";
        controlButton.type = "button";

        controlButton.disable = () => {
            controlButton.disabled = true;
            controlButton.style.color = "rgb(195,195,195)";
        }

        controlButton.enable = () => {
            controlButton.disabled = false;
            controlButton.style.color = "rgb(25,25,25)";
        }

        // Setup the click event listeners: simply set the map to Chicago.
        controlButton.addEventListener("click", () => {
            map.clearZone();
        });

        return controlButton;
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('filamentDrawableMap', (state = null) => ({
            state: state,
            onGeolocationSuccess(data) {
                console.log(data);
            },
            init() {
                let component = document.getElementById("{{ $getId() }}");
                if (component) {
                    data = component._x_dataStack[0];
                }

                data.state = this.state;
            }
        }))
    });

    document.addEventListener('alpine:initialized', () => {
        window.initMap = initMap;
        window.addEventListener('dark-mode-toggled', event => {
            var newMode = event.detail;
            $mode = newMode;
        });

        // Append the 'script' element to 'head'
        document.head.appendChild(script);
    });
</script>
@endpush
