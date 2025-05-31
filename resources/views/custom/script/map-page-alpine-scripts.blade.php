<script>
    // Global States
    //------------------------------------------------------
    Alpine.store('map', {
        map: null,
        defaultLayer: OSMBase,
        mapView: [35.715298, 51.404343],
        enableGesture: true,

        initMap() {
            return this.map = L.map(document.getElementById('map'), {
                pmIgnore: false,
                fullscreenControl: true,
                // gestureHandling: this.enableGesture,
                // gestureHandlingOptions: {
                //     text: {
                //         touch: "جهت جابجایی نقشه از دو انگشت استفاده نمایید",
                //         scroll: "جهت بزرگنمایی نقشه از کلید Ctrl + Scroll استفاده نمایید.",
                //         scrollMac: "جهت بزرگنمایی نقشه از کلید ⌘ + Scroll استفاده نمایید."
                //     }
                // }
            })
                .setView(this.mapView, 11)
                .addLayer(this.defaultLayer);
        },

        setMapView(view) {
            this.mapView = view;
        }
    })

    // Track Player
    //------------------------------------------------------
    Alpine.data('trackplayer', () => ({
        track: [],
        trackPlayer: null,
        map: null,
        speeds: [1, 2, 3, 4, 5],
        currentProgress: 0,
        isPlaying: false,
        currentSpeed: 1,

        init() {

            $wire.on('trips-fetched', (locations) => {
                if (locations.length > 0) {
                    this.map = Alpine.store('map').map;
                    this.prepareTrack(Object.values(locations[0]));
                }
            })

            this.initializeRangeSlider();
            // window.addEventListener("resize", this.initializeRangeSlider);

        },

        prepareTrack(deviceLocations) {
            if (deviceLocations.length > 0) {
                this.track = deviceLocations.at(-1).map(location => [
                    parseFloat(location.lat),
                    parseFloat(location.long)
                ]);
            }
        },

        initMap() {
            this.map.setView(this.track[0], 13, {
                animate: true,
                duration: 1
            });


            this.trackPlayer = new L.TrackPlayer(this.track, {
                speed: 600 * this.currentSpeed,
                markerIcon: L.divIcon({
                    html: `<div><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" x="0" y="0" fill="#0D311B" viewBox="0 0 29 29" xml:space="preserve" width="29" height="29"><style>.st3{fill:#0d311b}</style><path style="fill:#031108" d="M29 14.5A14.5 14.5 0 0 1 14.5 29 14.5 14.5 0 0 1 0 14.5a14.5 14.5 0 0 1 29 0"/><g style="opacity:.2"><path class="st3" d="M14.5 7.613 7.975 22.294l6.525-3.263 6.525 3.263z"/><path class="st3" d="M21.025 22.883c-.091 0-.181 0-.272-.045L14.5 19.711l-6.253 3.127c-.227.136-.498.091-.68-.091s-.227-.453-.136-.68l6.525-14.636a.589.589 0 0 1 1.088 0l6.525 14.636a.63.63 0 0 1-.136.68q-.204.136-.408.136M14.5 18.397c.091 0 .181 0 .272.045l4.984 2.492L14.5 9.153 9.244 20.98l4.984-2.492c.091-.045.181-.091.272-.091"/></g><path style="fill:#fff;stroke:#fff;stroke-width:3;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10" d="M14.5 6.797 8.111 21.161l6.389-3.217 6.389 3.217z"/></svg></div>`,
                    className: 'custom-marker',
                    iconSize: [32, 32],
                }),
            }).addTo(this.map);

            this.setupTrackPlayerEvents();
        },

        setupTrackPlayerEvents() {
            this.trackPlayer.on('progress', (progress) => {
                this.currentProgress = progress * 100;
                this.initializeRangeSlider()
            });

            this.trackPlayer.on('finished', () => {
                this.isPlaying = false;
            });
        },

        togglePlay() {
            this.$dispatch('remove-all');

            if (this.isPlaying) {
                this.trackPlayer.pause();
                this.isPlaying = false;
            } else {
                // Reinitialize if not already initialized
                if (!this.trackPlayer) this.initMap();
                this.trackPlayer.start();
                this.isPlaying = true;
            }
        },

        setProgress(value) {
            this.trackPlayer.setProgress(value / 100);
        },

        removeTracker() {
            if (this.trackPlayer) {
                this.trackPlayer.remove();
                this.$dispatch('appear-waypoints');
                this.trackPlayer = null;
                this.currentProgress = 0;
                this.currentSpeed = 1;
                this.track = [];
                this.isPlaying = false;
            }
        },

        changeSpeed() {
            const speedIndex = (this.speeds.indexOf(this.currentSpeed) + 1) % this.speeds.length;
            this.currentSpeed = this.speeds[speedIndex] === 0 ? 1 : this.speeds[speedIndex];

            if (this.trackPlayer) {
                this.trackPlayer.setSpeed(600 * this.currentSpeed);
            }
        },

        get displaySpeed() {
            return this.currentSpeed !== 1 ? this.currentSpeed + 'X' : false;
        },

        get btnStatus() {
            return this.track.length > 0;
        },

        initializeRangeSlider() {
            const slider_input = this.$refs.slider_input,
                slider_thumb = this.$refs.slider_thumb,
                slider_line = this.$refs.slider_line;

            this.$nextTick(() => {
                slider_thumb.innerHTML = slider_input.value;
                const bulletPosition = (slider_input.value / slider_input.max),
                    space = slider_input.offsetWidth - slider_thumb.offsetWidth;

                slider_thumb.style.left = (bulletPosition * space) + 'px';
                slider_line.style.width = slider_input.value + '%';
            });
        },

        handleSliderInput(e) {
            let rangeValue = e.target.value;
            this.setProgress(rangeValue);
            this.initializeRangeSlider();
        },
    }))

    // Map
    //------------------------------------------------------
    Alpine.data('mapComponent', () => ({
        map: null,
        baseMaps: baseMaps,
        currentZoom: 16,
        control: null,
        markers: {},
        followerLine: {},
        pathCoordinates: {},
        snapedCoords: [],
        savedMarkers: {},
        drownGeofences: {},
        drawnWaypoints: {},
        circleMarkers: [],
        trips: null,
        snapMode: false,
        dirMode: true,
        routeDecorator: null,
        totalDistance: [],
        neshanApi: @js(env('NESHAN_API')),


        init() {
            let self = this;
            this.map = Alpine.store('map').initMap();

            L.control.layers(this.baseMaps, null, {
                position: 'topright'
            }).addTo(this.map);


            this.map.createPane('data-point');
            this.map.getPane('data-point').style.zIndex = 850;

            this.map.on('zoomend', () => this.currentZoom = this.map.getZoom());

            // Fixing Popup when zooming
            L.Popup.prototype._animateZoom = function (e) {
                if (!this._map) return;
                let pos = this._map._latLngToNewLayerPoint(this._latlng, e.zoom, e.center),
                    anchor = this._getAnchor();
                L.DomUtil.setPosition(this._container, pos.add(anchor));
            }


            this.map.pm.setLang("fa");

            // Snap Mode Button
            let snapSvg =
                '<svg version="1.1" id="Icons" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" xml:space="preserve" width="24" height="24" transform="scale(-1 1)"><style>.st0{fill:none;stroke:#000;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10}</style><path class="st0" d="m9.975 5.775 6.075 6.075c1.125 1.125 1.125 2.925 0 4.05s-2.925 1.125-4.05 0L5.925 9.825 2.4 13.35l6.075 6.075c3.075 3.075 8.025 3.075 11.1 0s3.075-8.025 0-11.1L13.5 2.25z"/><path class="st0" d="M2.425 13.343 5.978 9.79l2.015 2.015-3.553 3.553zm7.582-7.572 3.553-3.553 2.015 2.015-3.553 3.553z"/></svg>'
            let snapModeBtn = this.createToggleButton(snapSvg);
            this.map.addControl(new snapModeBtn.object());

            snapModeBtn.button.onclick = () => {
                this.snapMode = !this.snapMode;

                if (!this.snapMode) {
                    snapModeBtn.button.classList.remove('enabled');
                    snapModeBtn.button.classList.add('disabled');
                } else {
                    snapModeBtn.button.classList.remove('disabled');
                    snapModeBtn.button.classList.add('enabled');
                }

                if (this.trips) {
                    this.showWaypoints(this.trips);
                }
            }

            // arrow direction Mode Button
            let directionSvg =
                '<svg width="24" height="24" viewBox="0 0 0.72 0.72" xmlns="http://www.w3.org/2000/svg"><path d="M.569.168.438.561a.013.013 0 0 1-.026 0L.353.384A.03.03 0 0 0 .336.367L.159.308a.013.013 0 0 1 0-.026L.552.151a.013.013 0 0 1 .017.017" stroke="#464455" stroke-linecap="round" stroke-linejoin="round" stroke-width=".03"/></svg>';
            let dirBtn = this.createToggleButton(directionSvg);
            this.map.addControl(new dirBtn.object());

            dirBtn.button.onclick = () => {
                this.dirMode = !this.dirMode;

                if (!this.snapMode) {
                    dirBtn.button.classList.remove('enabled');
                    dirBtn.button.classList.add('disabled');
                } else {
                    dirBtn.button.classList.remove('disabled');
                    dirBtn.button.classList.add('enabled');
                }

                if (this.trips) {
                    this.showWaypoints(this.trips);
                }
            }

            // document.addEventListener('fullscreenchange', () => this.checkFullscreen());
            // this.checkFullscreen();

            // Livewire Events
            $wire.on('geo-fetched', (data) => {
                if (data[0].length > 0) this.showGeofences(data[0]);
            });

            // Initial Map Waypoint
            $wire.on('trips-fetched', (trips) => {
                if (trips.length > 0) {
                    this.trips = Object.values(trips[0]);
                    this.showWaypoints(Object.values(trips[0]));
                }
            })

            // Reset
            $wire.on('geo-reset', () => this.removeGeofences())
            $wire.on('trips-reset', () => this.removeWayPoints())


            this.updateLocations($wire.deviceLocations);
            $wire.on('locationUpdated', () => this.updateLocations($wire.deviceLocations));

            setInterval(() => {
                if ($wire.onlineMode) {
                    $wire.on('locationUpdated', () => this.updateLocations($wire.deviceLocations));
                }
            }, 4000)
        },

        // Handle The gesture in mobile map
        //-----------------------------------
        checkFullscreen() {
            Alpine.store('map').enableGesture = !document.fullscreenElement;
        },

        // Handle The Devices live location
        //-----------------------------------
        updateLocations(locations) {
            // Remove old Markers
            Object.values(this.markers).forEach(marker => marker.remove());
            this.markers = {};

            Object.values(this.followerLine).forEach(line => line.remove());
            this.followerLine = {};

            let bounds = L.latLngBounds();


            // Add New Markers
            Object.entries(locations).forEach(([deviceId, data]) => {
                if (!data?.lat || !data?.long) return;

                const position = [parseFloat(data.lat), parseFloat(data.long)];
                if (isNaN(position[0]) || isNaN(position[1])) return;

                const status = this.getMarkerStatus(data);
                const marker = L.marker(position, {
                    icon: this.createCustomIcon(status, parseFloat(JSON.parse(data
                        .device_stats)?.direction))
                }).bindPopup(this.createPopupContent(data, 'درحال دریافت آدرس...'))

                marker.on('popupopen', () => {
                    this.fetchAddress(position, (address) => {
                        marker.setPopupContent(this.createPopupContent(data, address))
                    })
                })

                // .bindPopup(this.createPopupContent(data));
                marker.addTo(this.map);
                this.markers[deviceId] = marker;

                // Handle Follower Line if onlineMode is true
                if ($wire.onlineMode) {
                    if (!this.pathCoordinates) this.pathCoordinates = {};
                    if (!this.pathCoordinates[deviceId]) this.pathCoordinates[deviceId] = [];

                    this.pathCoordinates[deviceId].push(position);

                    // Keep only the last 10 points
                    if (this.pathCoordinates[deviceId].length > 10) {
                        this.pathCoordinates[deviceId].shift();
                    }

                    // Create or update the follower line
                    const followerLine = L.polyline(this.pathCoordinates[deviceId], {color: 'green'});

                    followerLine.addTo(this.map);
                    this.followerLine[deviceId] = followerLine;
                }


                bounds.extend(position);
            });

            // Set Map View
            if ($wire.selected.length > 0) {
                const selectedLocation = locations[$wire.selected.at(-1)];
                if (selectedLocation) {
                    const position = [parseFloat(selectedLocation.lat), parseFloat(selectedLocation.long)];
                    if (!isNaN(position[0]) && !isNaN(position[1])) {
                        this.map.setView(position, this.currentZoom, {
                            animate: true,
                            duration: 1
                        });
                    }
                }
            } else if (bounds.isValid()) {
                this.map.fitBounds(bounds, {
                    padding: [50, 50],
                    maxZoom: 14,
                    animate: true,
                    duration: 1
                });
            }
        },

        fetchAddress(position, callback) {
            const [lat, lng] = position;
            fetch(`https://api.neshan.org/v5/reverse?lat=${lat}&lng=${lng}`, {
                method: "GET",
                headers: {
                    "Api-key": this.neshanApi
                },
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'OK') {
                        const address = data.formatted_address || 'آدرس پیدا نشد!';
                        callback(address);
                    } else {
                        callback('خطا در دریافت آدرس...')
                    }
                })
                .catch(() => {
                    callback('خطا در دریافت آدرس...');
                });
        },

        createCustomIcon(status = 'active', degree) {
            const markerIcons = {
                active: `<svg xmlns="http://www.w3.org/2000/svg" width="32px" height="32px" viewBox="-2 -2 24 24"><path fill="#4CAF50" stroke="#000" stroke-width="2" d="m18.919 2.635l-5.953 16.08c-.376 1.016-1.459 1.538-2.418 1.165a1.85 1.85 0 0 1-1.045-1.054l-1.887-4.77a3.7 3.7 0 0 0-1.955-2.052l-4.542-1.981C.174 9.61-.256 8.465.157 7.465a1.97 1.97 0 0 1 1.067-1.079L16.54.136c.967-.395 2.04.101 2.395 1.109c.157.446.151.94-.015 1.39z"/></svg>`,
                inactive: `<svg xmlns="http://www.w3.org/2000/svg" width="32px" height="32px" viewBox="-2 -2 24 24"><path fill="#FF5725" stroke="#000" stroke-width="2" d="m18.919 2.635l-5.953 16.08c-.376 1.016-1.459 1.538-2.418 1.165a1.85 1.85 0 0 1-1.045-1.054l-1.887-4.77a3.7 3.7 0 0 0-1.955-2.052l-4.542-1.981C.174 9.61-.256 8.465.157 7.465a1.97 1.97 0 0 1 1.067-1.079L16.54.136c.967-.395 2.04.101 2.395 1.109c.157.446.151.94-.015 1.39z"/></svg>`,
                warning: `<svg xmlns="http://www.w3.org/2000/svg" width="32px" height="32px" viewBox="-2 -2 24 24"><path fill="#FFC107" stroke="#000" stroke-width="2" d="m18.919 2.635l-5.953 16.08c-.376 1.016-1.459 1.538-2.418 1.165a1.85 1.85 0 0 1-1.045-1.054l-1.887-4.77a3.7 3.7 0 0 0-1.955-2.052l-4.542-1.981C.174 9.61-.256 8.465.157 7.465a1.97 1.97 0 0 1 1.067-1.079L16.54.136c.967-.395 2.04.101 2.395 1.109c.157.446.151.94-.015 1.39z"/></svg>`
            };


            return L.divIcon({
                html: `<div style="transform: rotate(${Number(degree || 0)}deg);">${markerIcons[status]}</div>`,
                className: 'custom-marker',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });
        },

        createPopupContent(data, address) {
            return `
            <p style="margin: 0 !important; padding: 3px 0 3px 20px !important; white-space: nowrap; vertical-align: middle !important; text-align: right">
                <span style="margin-left: 5px"><i class="icofont icofont-micro-chip"></i></span>${data.device?.name} - ${data.device?.model}
            </p>
            <p style="margin: 0 !important; padding: 3px 0 3px 20px !important; white-space: nowrap; vertical-align: middle !important; text-align: right">
                <span style="margin-left: 5px"><i class="fa fa-car"></i></span>${data.vehicle?.name || 'نامشخص'}
            </p>
            <p style="margin: 0 !important; padding: 3px 0 3px 20px !important; white-space: nowrap; vertical-align: middle !important; text-align: right">
                <span style="margin-left: 5px"><i class="icofont icofont-bar-code"></i></span>${data.vehicle?.license_plate || 'نامشخص'}
            </p>
            <p style="margin: 0 !important; padding: 3px 0 3px 20px !important; white-space: nowrap; vertical-align: middle !important; text-align: right">
                <span style="margin-left: 5px"><i class="fa fa-user"></i></span>${data.user?.name} - ${data.user?.phone}
            </p>
            <p style="margin: 0 !important; padding: 3px 0 3px 20px !important; white-space: nowrap; vertical-align: middle !important; text-align: right">
                <span style="margin-left: 5px"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock" style="width: 15px;height: 15px"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg></span>${data.name}
            </p>
            <p style="margin: 0 !important; padding: 3px 0 3px 20px !important; white-space: nowrap; vertical-align: middle !important; text-align: right">
                <span style="margin-left: 5px"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin" style="width: 15px;height: 15px"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg></span>
                <a href="https://maps.google.com/?q=${data.lat},${data.long}" rel="nofollow noopener noreferrer" target="_blank">${parseFloat(data.lat).toFixed(4)},${parseFloat(data.long).toFixed(4)}</a>
            </p>
            <p style="margin: 0 !important; padding: 3px 0 3px 20px !important; vertical-align: middle !important; text-align: right">
                <span style="margin-left: 5px"><i class="icofont icofont-pin"></i></span>${address}
            </p>
            <p style="margin: 0 !important; padding: 3px 0 3px 20px !important; white-space: nowrap; vertical-align: middle !important; text-align: right">
                <span style="margin-left: 5px"><i class="icofont icofont-street-view"></i></span>
                <a href="https://www.google.com/maps?q&layer=c&cbll=${data.lat},${data.long}" rel="nofollow noopener noreferrer" target="_blank">نمایش خیابانی 360 درجه (صفحه جدید)</a>
            </p>
            <p style="margin: 0 !important; padding: 3px 0 3px 20px !important; white-space: nowrap; vertical-align: middle !important; text-align: right">
                <span style="margin-left: 5px"><i class="icofont icofont-speed-meter"></i></span> ${Math.round(JSON.parse(data.device_stats)?.speed || 0)} کیلومتر ‌بر ساعت
            </p>
            ${data.distance ?
                `<p style="margin: 0 !important; padding: 3px 0 3px 20px !important; white-space: nowrap; vertical-align: middle !important; text-align: right">
                                                                                                                                                                                                                                                                                                                                                    <span style="margin-left: 5px"><i class="fa fa-solid fa-flag-checkered"></i></span> ${data.distance} کیلومتر
                                                                                                                                                                                                                                                                                                                                                </p>`
                : ''
            }
        `;
        },

        createToggleButton(svgContent, position = 'topleft') {
            let button = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom');
            button.innerHTML = svgContent;
            button.className = 'enabled';
            button.className = 'leaflet-control-custom';

            button.onclick = function () {
                if (button.classList.contains('disabled')) {
                    button.classList.remove('disabled');
                    button.classList.add('enabled');
                } else {
                    button.classList.remove('enabled');
                    button.classList.add('disabled');
                }
            };

            return {
                button: button,
                object: L.Control.extend({
                    onAdd: function (map) {
                        return button;
                    },
                    onRemove: function (map) {
                        // Nothing to do here
                    },
                    getPosition: function () {
                        return position;
                    }
                })
            };
        },

        getMarkerStatus(data) {
            const lastUpdate = new Date(data.created_at);
            const now = new Date();
            const diffMinutes = Math.floor((now - lastUpdate) / 1000 / 60);

            if (diffMinutes < 5) return 'active';
            if (diffMinutes < 15) return 'warning';
            return 'inactive';
        },

        // Handle geofences
        //-----------------------------------
        showGeofences(geoFences) {
            this.removeGeofences();
            geoFences.forEach(fence => {
                try {
                    // make Geofence Color
                    const color = this.getColorForGeofence(fence.id);

                    if (fence.points) {
                        const latlngCoordinates = fence.points.map(coord => [coord[1], coord[0]]);
                        const polygon = L.polygon(latlngCoordinates, {
                            color: color
                        }).addTo(this.map);
                        const label = L.marker(polygon.getBounds().getCenter(), {
                            icon: L.divIcon({
                                className: 'geofence-label',
                                html: `<span class="fw-bold bg-white d-block p-1 mb-1 text-center rounded">${fence.name}</span>`,
                                iconSize: [100, 20],
                            })
                        }).addTo(this.map);

                        this.drownGeofences[fence.id] = {
                            polygon
                        };
                        this.drownGeofences[fence.id].label = label;

                    }
                } catch (error) {
                    console.error("Invalid geometry format:", error);
                }
            });
        },

        removeGeofences() {
            Object.values(this.drownGeofences).forEach(({
                                                            polygon,
                                                            label
                                                        }) => {
                this.map.removeLayer(polygon);
                if (label) {
                    this.map.removeLayer(label);
                }
            });
            this.drownGeofences = {};
        },

        getColorForGeofence(geofenceId) {
            const colors = [
                "#FF5733", "#33FF57", "#3357FF", "#FF33A1", "#33FFA1", "#A133FF", "#3D27CD", "#DEF61D",
                "#811414", "#382F18", "#F3D24D", "#3ABA61"
            ];
            const savedColors = JSON.parse(localStorage.getItem('geofenceColors')) || {};

            if (!savedColors[geofenceId]) {
                savedColors[geofenceId] = colors[Math.floor(Math.random() * colors.length)];
                localStorage.setItem('geofenceColors', JSON.stringify(savedColors));
            }

            return savedColors[geofenceId];
        },

        // Handle The Devices trips
        //-----------------------------------
        showWaypoints(trips) {

            const startIcon = L.icon({
                iconUrl: '{{ asset('assets/libs/leaflet/images/map-start.svg') }}',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            const endIcon = L.icon({
                iconUrl: '{{ asset('assets/libs/leaflet/images/map-end.svg') }}',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });


            this.removeWayPoints();
            this.circleMarkers = []; // Array to store circle markers

            trips.forEach((trip, i) => {
                const gpsData = trip.map(t => ({
                    lat: t.lat,
                    lng: t.long,
                    speed: JSON.parse(t.device_stats).speed,
                    datetime: new Date(t.created_at)
                }));

                const chunkSize = 500;
                const routeChunks = [];
                for (let j = 0; j < trip.length; j += chunkSize) {
                    routeChunks.push(trip.slice(j, j + chunkSize));
                }

                let allRouteCoords = [];

                routeChunks.forEach((chunk, chunkIndex) => {
                    const routeCoords = chunk.map(coord => [parseFloat(coord.lat), parseFloat(coord.long)]);
                    allRouteCoords = allRouteCoords.concat(routeCoords);

                    let polyline, snapedRoute;

                    if (this.snapMode) {
                        snapedRoute = L.Routing.control({
                            // router: L.Routing.osrmv1({
                            //     serviceUrl: 'https://test.aronict.com/app/route/v1',
                            //     profile: 'driving',
                            //     routingOptions: {
                            //         alternatives: false,
                            //         continue_straight: true,
                            //     }
                            // }),
                            waypoints: routeCoords,
                            waypointMode: 'snap',
                            draggableWaypoints: false,
                            routeWhileDragging: false,
                            addWaypoints: false,
                            lineOptions: {
                                styles: [{
                                    color: "#F50A0AFF",
                                    weight: 5,
                                    opacity: this.snapMode ? 0.9 : 0
                                }]
                            },
                            createMarker: function () {
                            },
                        }).addTo(this.map);

                        snapedRoute.on('routesfound', (e) => {
                            const routes = e.routes;

                            if (routes.length > 0) {
                                if (this.dirMode)
                                    this.addRouteDirection(allRouteCoords);
                            }

                            // Assign totalDistance to every point in the chunk
                            const totalDistance = (routes[0].summary.totalDistance / 1000).toFixed(2);
                            chunk.forEach(point => point.distance = totalDistance);
                        });

                    } else {
                        polyline = L.polyline(routeCoords, {
                            color: "#F50A0AFF",
                            weight: 5,
                            smoothFactor: 1.5,
                            opacity: !this.snapMode ? 0.9 : 0
                        }).addTo(this.map);

                        if (this.dirMode)
                            this.addRouteDirection(allRouteCoords);
                    }

                    if (polyline) {
                        this.map.fitBounds(polyline.getBounds(), {
                            animate: true,
                            duration: 2
                        });
                    }

                    routeCoords.forEach((coord, i) => {
                        const circle = L.circleMarker(coord, {
                            radius: 5,
                            pane: 'data-point',
                            color: "#3388ff",
                            fillOpacity: 0.5,
                        }).addTo(this.map);

                        circle.on('click', (event) => {
                            this.fetchAddress(coord, (address) => {
                                L.popup()
                                    .setLatLng(event.latlng)
                                    .setContent(this.createPopupContent(chunk[i], address))
                                    .openOn(this.map);
                            });
                        });

                        this.circleMarkers.push(circle);
                    });

                    this.map.on('zoomend', () => {
                        const currentZoom = this.map.getZoom();
                        this.circleMarkers.forEach((circle) => {
                            if (currentZoom >= 15) {
                                circle.addTo(this.map); // Show circles
                            } else {
                                this.map.removeLayer(circle); // Hide circles
                            }
                        });
                    });

                    if (!this.drawnWaypoints[i]) this.drawnWaypoints[i] = {};
                    this.drawnWaypoints[i][chunkIndex] = {
                        polyline: polyline,
                        snapedRoute: snapedRoute
                    };
                });

                const startMarker = L.marker(allRouteCoords[0], {
                    icon: startIcon,
                    title: 'شروع'
                }).addTo(this.map);

                const endMarker = L.marker(allRouteCoords.at(-1), {
                    icon: endIcon,
                    title: 'پایان'
                }).addTo(this.map);

                this.fetchAddress(allRouteCoords[0], (startAddress) => {
                    startMarker.bindPopup(this.createPopupContent(trip[0], startAddress));
                });

                this.fetchAddress(allRouteCoords.at(-1), (endAddress) => {
                    endMarker.bindPopup(this.createPopupContent(trip.at(-1), endAddress));
                });


                this.drawnWaypoints[i].markers = {
                    startMarker: startMarker,
                    endMarker: endMarker
                };
            });
        },

        addRouteDirection(route) {
            if (this.routeDecorator) {
                this.map.removeLayer(this.routeDecorator);
            }

            if (!route) return;

            this.routeDecorator = L.polylineDecorator(route, {
                patterns: [{
                    repeat: 150,
                    symbol: L.Symbol.arrowHead({
                        pixelSize: 15,
                        pathOptions: {
                            color: '#000',
                            fillOpacity: 0.8,
                            weight: 0
                        }
                    })
                }]
            }).addTo(this.map);
        },

        removeWayPoints() {

            Object.values(this.drawnWaypoints).forEach((waypointGroup) => {
                Object.values(waypointGroup).forEach((route) => {
                    if (route.polyline) this.map.removeLayer(route.polyline);
                    if (route.snapedRoute) route.snapedRoute.remove();
                });

                if (waypointGroup.markers) {
                    Object.values(waypointGroup.markers).forEach((marker) => {
                        if (marker) this.map.removeLayer(marker);
                    });
                }

                if (this.routeDecorator) {
                    this.map.removeLayer(this.routeDecorator);
                }
            });

            this.circleMarkers.forEach((circle) => {
                if (circle) this.map.removeLayer(circle);
            });

            this.drawnWaypoints = {};
            this.circleMarkers = [];
        },

    }));


    // DatePicker (Enter Time)
    //------------------------------------------------------
    Alpine.data('dateTimeRange', (input) => ({
        flatpickrInstance: null,
        disabled: true,
        placeholder: 'لطفا ابتدا دستگاه را انتخاب کنید!',

        init() {
            this.initializeFlatpickr();
            this.updateInputState();

            $wire.on('locationUpdated', () => {
                this.updateInputState();

                if (this.flatpickrInstance) {
                    this.flatpickrInstance.destroy();
                }

                this.initializeFlatpickr();
            });


        },

        updateInputState() {
            if ($wire.selected.length === 0) {
                this.disabled = true;
                this.placeholder = 'لطفا ابتدا دستگاه را انتخاب کنید!';
            } else {
                this.disabled = false;
                this.placeholder = 'لطفا تاریخ و زمان را انتخاب کنید!';
            }

            if (this.flatpickrInstance) {
                const altInput = this.flatpickrInstance.altInput;
                if (altInput) {
                    altInput.disabled = this.disabled;
                    altInput.placeholder = this.placeholder;
                }
                this.flatpickrInstance.input.disabled = this.disabled;
                this.flatpickrInstance.input.placeholder = this.placeholder;
            }
        },

        initializeFlatpickr() {
            this.flatpickrInstance = flatpickr(input, {
                mode: "range",
                enableTime: true,
                time_24hr: true,
                locale: "fa",
                altInput: true,
                altFormat: 'Y/m/d - H:i',
                maxDate: "today",
                disableMobile: true,
                disabled: this.disabled,
                placeholder: this.placeholder,
                // onChange: (selectedDates, dateStr, instance) => {
                //     if (selectedDates.length === 2) {
                //         const $startDate = selectedDates[0];
                //         const $endDate = selectedDates[1];
                //
                //         if (
                //             $startDate.getDate() === $endDate.getDate() &&
                //             $startDate.getMonth() === $endDate.getMonth() &&
                //             $startDate.getFullYear() === $endDate.getFullYear() &&
                //             $startDate.getHours() === $endDate.getHours() &&
                //             $startDate.getMinutes() === $endDate.getMinutes()
                //         ) {
                //             $endDate.setHours(23);
                //             $endDate.setMinutes(59);
                //
                //             instance.setDate([$startDate, $endDate]);
                //         }
                //
                //     }
                //
                // },
                onClose: (selectedDates, dateStr) => {
                    $wire.set('dateTimeRange', dateStr);
                }
            });
        }
    }));
</script>
