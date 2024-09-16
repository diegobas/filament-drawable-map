<style>
    /* The popup bubble styling. */
    .popup-bubble {
        /* Position the bubble centred-above its parent. */
        position: absolute;
        top: 0;
        left: 0;
        transform: translate(-50%, -100%);
        /* Style the bubble. */
        background-color: white;
        padding: 5px;
        border-radius: 5px;
        font-family: sans-serif;
        overflow-y: auto;
        max-height: 60px;
        box-shadow: 0px 2px 10px 1px rgba(0, 0, 0, 0.5);
    }

    /* The parent of the bubble. A zero-height div at the top of the tip. */
    .popup-bubble-anchor {
        /* Position the div a fixed distance above the tip. */
        position: absolute;
        width: 100%;
        bottom: 8px;
        left: 0;
    }

    /* This element draws the tip. */
    .popup-bubble-anchor::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        /* Center the tip horizontally. */
        transform: translate(-50%, 0);
        /* The tip is a https://css-tricks.com/snippets/css/css-triangle/ */
        width: 0;
        height: 0;
        /* The tip is 8px high, and 12px wide. */
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-top: 8px solid white;
    }

    /* JavaScript will position this div at the bottom of the popup tip. */
    .popup-container {
        cursor: auto;
        height: 0;
        position: absolute;
        padding: 4px;
        /* The max width of the info window. */
        width: 200px;
    }
</style>

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
    @endphp

    <div x-data="{
            mode: @js($getMode()),
            map: null,
            popup: null,
            titles: @js($getTitles()),
            colors: @js($getColors()),
            polygons: @js($getPolygons()),
            init() {
                let component = document.getElementById(@js($getId()))
                if (component) {
                    this.data = component._x_dataStack[0]
                }

                const gmaps = 'https://maps.googleapis.com/maps/api/js?key={{ config('filament-drawable-map.providers.google.key') }}&callback=Function.prototype&libraries=places&language={{ App::getLocale() }}'
                if (!document.querySelector('script[src=\x22' + gmaps + '\x22]')) {
                    this.load(gmaps).then(() => this.initMap())
                } else {
                    this.initMap()
                }
            },
            load(gmaps) {
                return new Promise(resolve => {
                    const script = document.createElement('script');
                    script.type = 'text/javascript';
                    script.src = gmaps
                    script.async = true;
                    script.onload = resolve;
                    document.head.appendChild(script);
                })
            },
            toggleMode() {
                if (this.mode === 'dark') {
                    this.mode = 'light'
                } else {
                    this.mode = 'dark'
                }

                this.applyMode()
            },
            applyMode() {
                const darkMode = [
                    { elementType: 'geometry', stylers: [{ color: '#242f3e' }] },
                    { elementType: 'labels.text.stroke', stylers: [{ color: '#242f3e' }] },
                    { elementType: 'labels.text.fill', stylers: [{ color: '#746855' }] },
                    {
                        featureType: 'administrative.locality',
                        elementType: 'labels.text.fill',
                        stylers: [{ color: '#d59563' }],
                    },
                    {
                        featureType: 'poi',
                        elementType: 'labels.text.fill',
                        stylers: [{ color: '#d59563' }],
                    },
                    {
                        featureType: 'poi.park',
                        elementType: 'geometry',
                        stylers: [{ color: '#263c3f' }],
                    },
                    {
                        featureType: 'poi.park',
                        elementType: 'labels.text.fill',
                        stylers: [{ color: '#6b9a76' }],
                    },
                    {
                        featureType: 'road',
                        elementType: 'geometry',
                        stylers: [{ color: '#38414e' }],
                    },
                    {
                        featureType: 'road',
                        elementType: 'geometry.stroke',
                        stylers: [{ color: '#212a37' }],
                    },
                    {
                        featureType: 'road',
                        elementType: 'labels.text.fill',
                        stylers: [{ color: '#9ca5b3' }],
                    },
                    {
                        featureType: 'road.highway',
                        elementType: 'geometry',
                        stylers: [{ color: '#746855' }],
                    },
                    {
                        featureType: 'road.highway',
                        elementType: 'geometry.stroke',
                        stylers: [{ color: '#1f2835' }],
                    },
                    {
                        featureType: 'road.highway',
                        elementType: 'labels.text.fill',
                        stylers: [{ color: '#f3d19c' }],
                    },
                    {
                        featureType: 'transit',
                        elementType: 'geometry',
                        stylers: [{ color: '#2f3948' }],
                    },
                    {
                        featureType: 'transit.station',
                        elementType: 'labels.text.fill',
                        stylers: [{ color: '#d59563' }],
                    },
                    {
                        featureType: 'water',
                        elementType: 'geometry',
                        stylers: [{ color: '#17263c' }],
                    },
                    {
                        featureType: 'water',
                        elementType: 'labels.text.fill',
                        stylers: [{ color: '#515c6d' }],
                    },
                    {
                        featureType: 'water',
                        elementType: 'labels.text.stroke',
                        stylers: [{ color: '#17263c' }],
                    },
                ]

                if (this.map !== null && this.mode === 'dark') {
                    this.map.setOptions({
                        styles: darkMode
                    })
                } else {
                    this.map.setOptions({
                        styles: []
                    })
                }
            },
            initMap() {
                var div = document.getElementById('filament-viewable-map-map')
                var location = {
                    lat: {{ config('filament-drawable-map.location.latitude') }},
                    lng: {{ config('filament-drawable-map.location.longitude') }}
                };
                var mode = @js($getMode());
                var polygonOptions = {
                    strokeColor: @js($getColor()) ?? '#FF0000',
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: @js($getColor()) ?? '#FF0000',
                    fillOpacity: 0.35,
                    draggable: false,
                    clickable: true,
                    geodesic: true,
                }

                class Popup extends google.maps.OverlayView {
                    position;
                    containerDiv;
                    contentElement;
                    constructor(content) {
                        super();
                        this.contentElement = content;
                        this.contentElement.classList.add('popup-bubble');

                        const bubbleAnchor = document.createElement('div');
                        bubbleAnchor.classList.add('popup-bubble-anchor');
                        bubbleAnchor.appendChild(this.contentElement);

                        this.containerDiv = document.createElement('div');
                        this.containerDiv.setAttribute('id', 'popup-container');
                        this.containerDiv.classList.add('popup-container');
                        this.containerDiv.appendChild(bubbleAnchor);

                        Popup.preventMapHitsAndGesturesFrom(this.containerDiv);
                    }

                    onAdd() {
                        this.getPanes().floatPane.appendChild(this.containerDiv);
                    }

                    onRemove() {
                        if (this.containerDiv.parentElement) {
                            this.containerDiv.parentElement.innerHTML = ''
                        }
                    }

                    setPosition(position) {
                        if (position && position !== this.position) {
                            this.position = position
                            this.draw()
                        }
                    }

                    setContent(text) {
                        if (this.contentElement) {
                            this.contentElement.innerHTML = text
                        }
                    }

                    draw() {
                        if (this.position && this.getProjection()) {
                            const divPosition = this.getProjection().fromLatLngToDivPixel(
                                this.position
                            );

                            const display =
                                Math.abs(divPosition.x) < 4000 && Math.abs(divPosition.y) < 4000
                                    ? 'block'
                                    : 'none';

                            if (display === 'block') {
                                const containerDivStyle = this.containerDiv.style;
                                containerDivStyle.transition = 'all 0.4s ease-out';
                                containerDivStyle.transform = `translate(${divPosition.x}px, ${divPosition.y}px)`;
                                containerDivStyle.opacity = 1;
                            } else {
                                this.containerDiv.style.opacity = 0;
                                this.containerDiv.addEventListener('transitionend', () => {
                                    this.containerDiv.style.transform = '';
                                    this.containerDiv.style.transition = '';
                                }, { once: true });
                            }
                        }
                    }
                }

                this.map = new google.maps.Map(div, {
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
                    gestureHandling: @if ($isDisabled() ) 'none' @else 'cooperative' @endif,
                    clickableIcons: @if ($isDisabled() ) false @else true @endif
                })

                if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark')
                    this.mode = 'dark'
                } else {
                    document.documentElement.classList.remove('dark')
                    this.mode = 'light'
                }

                this.applyMode()

                this.popup = new Popup(
                    document.getElementById('filament-viewable-map-popup')
                )

                if (this.polygons) {
                    if (!google.maps.Polygon.prototype.getBounds) {
                        google.maps.Polygon.prototype.getBounds = function() {
                            var bounds = new google.maps.LatLngBounds()
                            this.getPath().forEach(function(element,index){bounds.extend(element)})

                            return bounds
                        }
                    }

                    var globalBounds = new google.maps.LatLngBounds()
                    this.polygons.forEach((item, index) => {
                        var polygon = new google.maps.Polygon(polygonOptions)
                        polygon.setPath(item)
                        polygon.setOptions({
                            strokeColor: this.colors[index] ?? '#FF0000',
                            fillColor: this.colors[index] ?? '#FF0000',
                        })
                        polygon.getPath().forEach(function(element,index){globalBounds.extend(element)})
                        polygon.addListener('mouseover', (e) => {
                            if (this.titles) {
                                this.popup.setPosition(polygon.getBounds().getCenter())
                                this.popup.setContent(this.titles[index])
                                this.popup.setMap(this.map)
                            }
                        })
                        polygon.addListener('mouseout', (e) => this.popup.setMap(null))
                        polygon.addListener('click', function (e) {
                            var bounds = polygon.getBounds()
                            if (bounds) {
                                this.map.fitBounds(bounds)
                                this.map.panToBounds(bounds)
                            }
                        })
                        polygon.setMap(this.map)
                        if (globalBounds) {
                            this.map.fitBounds(globalBounds)
                            this.map.panToBounds(globalBounds)
                        }
                    })
                }
            }
        }"

        x-on:dark-mode-toggled.window="toggleMode"

        {{
        $attributes->
            merge($getExtraAttributes())->class([
                'flex items-center space-x-2 rtl:space-x-reverse group filament-forms-text-input-component'])
        }}
        wire:ignore id="{{ $getId() }}">

        @if ($label = $getPrefixLabel())
        <span @class($affixLabelClasses)>
            {{ $label }}
        </span>
        @endif

        <div class="flex-1">
            <div id="filament-viewable-map-container">
                <div id="filament-viewable-map-map" style="height:400px;"
                    class="border border-gray-300 block w-full transition duration-75 rounded-lg shadow-sm focus:border-primary-600 focus:ring-1 focus:ring-inset focus:ring-primary-600 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-600 dark:border-gray-600">
                </div>
                <div id="filament-viewable-map-popup"></div>
            </div>
        </div>
    </div>

</x-dynamic-component>
