import { Loader } from "@googlemaps/js-api-loader"

export default function filamentDrawableMapPlugin({
    state,
    color,
    location,
    zoom,
    disabled,
    clearButtonLabel
}) {
    return {
        state,
        color,
        location,
        zoom,
        disabled,
        clearButtonLabel,

        theme: 'light',

        init: function () {

            this.theme =
                localStorage.getItem('theme') ??
                getComputedStyle(document.documentElement).getPropertyValue(
                    '--default-theme-mode',
                )

            if (this.state !== null && (typeof this.state === 'string' || this.state instanceof String)) {
                this.state = JSON.parse(this.state)
            }

            const loader = new Loader({
                apiKey: window.filamentData.api.key,
                version: "weekly",
            })

            loader.load().then(async () => {
                this.loadGMaps()
            })
        },

        loadGMaps: async function (state) {
            var editable = false
            var map = new google.maps.Map(document.getElementById("filament-drawable-map-map"), {
                center: { lat: this.location['latitude'] ?? 0, lng: this.location['longitude'] ?? 0 },
                zoom: this.zoom ?? 13,
                disableDefaultUI: true,
                zoomControl: true,
                gestureHandling: this.disabled ? 'none' : 'auto',
                clickableIcons: !this.disabled
            })

            this.applyMode(map, this.theme)

            var polygon = new google.maps.Polygon({
                strokeColor: this.color ?? "#FF0000",
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: this.color ?? "#FF0000",
                fillOpacity: 0.35,
                draggable: false,
                geodesic: true,
                editable: editable,
            })

            const centerControlDiv = document.createElement("div")
            const centerControl = this.createClearControl(map)
            centerControl.disable()
            centerControlDiv.appendChild(centerControl)
            map.controls[google.maps.ControlPosition.TOP_CENTER].push(centerControlDiv)

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
                        this.sync(polygon, path)
                    }

                    centerControl.disable();
                }.bind(this)
            }

            window.addEventListener('theme-changed', (event) => {
                this.theme = event.detail
                this.applyMode(map, this.theme)
            })

            if (this.state !== null) {
                const path = polygon.getPath()
                this.state.forEach((point, index) => {
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

            if (!this.disabled) {
                map.addListener('click', function(e) {
                    if (e.domEvent.ctrlKey) {
                        if (polygon) {
                            const path = polygon.getPath()
                            path.push(e.latLng)

                            if (!polygon.getMap()) {
                                polygon.setMap(map)
                            }
                        }

                        if (centerControl.disabled) {
                            centerControl.enable()
                        }
                    } else {
                        map.panTo(e.latLng)
                    }
                })

                document.addEventListener('keyup', function (event) {
                    if (event.key === "Control") {
                        editable = false;
                        if (polygon) { polygon.setEditable(editable); }
                        map.setOptions({
                            draggableCursor:'',
                            draggingCursor: ''});
                    }
                });

                document.addEventListener('keydown', function (event) {
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

                polygon.addListener('click', function (e) {
                    var bounds = polygon.getBounds()
                    if (bounds) {
                        map.fitBounds(bounds)
                        map.panToBounds(bounds)
                    }
                })

                const path = polygon.getPath()
                path.addListener('insert_at', () => this.sync(polygon, path))
                path.addListener('remove_at', () => this.sync(polygon, path))
                path.addListener('set_at', () => this.sync(polygon, path))
            } else {
                google.maps.event.clearListeners(map);
                google.maps.event.clearInstanceListeners(map);
            }
        },

        createClearControl: function (map) {
            const controlButton = document.createElement("button")

            // Set CSS for the control.
            controlButton.style.backgroundColor = "#fff"
            controlButton.style.border = "2px solid #fff"
            controlButton.style.borderRadius = "3px"
            controlButton.style.boxShadow = "0 2px 6px rgba(0,0,0,.3)"
            controlButton.style.color = "rgb(25,25,25)"
            controlButton.style.cursor = "pointer"
            controlButton.style.fontFamily = "Roboto,Arial,sans-serif"
            controlButton.style.fontSize = "16px"
            controlButton.style.lineHeight = "38px"
            controlButton.style.margin = "8px 0 22px"
            controlButton.style.padding = "0 5px"
            controlButton.style.textAlign = "center"
            controlButton.textContent = this.clearButtonLabel
            controlButton.title = this.clearButtonLabel
            controlButton.type = "button"

            controlButton.disable = () => {
                controlButton.disabled = true
                controlButton.style.color = "rgb(195,195,195)"
            }

            controlButton.enable = () => {
                controlButton.disabled = false
                controlButton.style.color = "rgb(25,25,25)"
            }

            controlButton.addEventListener("click", () => {
                map.clearZone()
            })

            return controlButton
        },

        sync: function (zone, path) {
            if (zone && zone.getMap() && path.length > 0) {
                const data = path.getArray().map((point) => new google.maps.LatLng(point.lat(), point.lng()))
                this.state = data
            } else {
                if (path.length === 0) {
                    this.state = null
                }
            }
        },

        applyMode(map, mode) {
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

            if (map !== null && mode === 'dark') {
                map.setOptions({
                    styles: darkMode
                })
            } else {
                map.setOptions({
                    styles: []
                })
            }
        }
    }
}
