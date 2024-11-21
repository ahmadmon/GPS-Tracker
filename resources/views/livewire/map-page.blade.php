<div>

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-12 d-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg>
                            </a>
                        </li>
                        <li class="breadcrumb-item dana">نقشه</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="row">
            <div class="col-md-4">
                <div class="email-right-aside bookmark-tabcontent">
                    <div class="card email-body radius-left">
                        <div class="ps-0">
                            <div class="tab-content">
                                <div id="pills-created" role="tabpanel">
                                    <div class="card mb-0">
                                        <div class="card-header d-flex">
                                            <h5>دستگاه ها</h5>
                                        </div>
                                        <div class="card-body p-0 device-sidebar overflow-hidden">
                                            <div class="col-md-12">
                                                <input class="form-control rounded-0" id="search" type="text"
                                                       placeholder="جستجو بر اساس نام یا شناسه..." aria-label="جسنجو"
                                                       wire:model.live.debounce.850ms="search">
                                                <x-input-error :messages="$errors->get('search')" class="mt-1"/>
                                            </div>
                                            <div class="taskadd visible-scroll">
                                                <div class="table-responsive text-nowrap">
                                                    <table class="table">
                                                        <tbody>
                                                        <tr>
                                                            <td @class(['w-100 d-none justify-content-start align-items-center', 'd-flex' => $errors->has('selected')])>
                                                                <x-input-error :messages="$errors->get('selected')"
                                                                               class="mt-1"/>
                                                            </td>
                                                            <td @class(['w-100 d-none justify-content-start align-items-center', 'd-flex' => $errors->has('selected.*')])>
                                                                <x-input-error
                                                                    :messages="$errors->get('selected.*')"
                                                                    class="mt-1"/>
                                                            </td>
                                                        </tr>
                                                        @forelse($devices as $key => $device)
                                                            <tr wire:key="{{ $device->id }}">
                                                                <td class="w-100 d-flex justify-content-between align-items-center">
                                                                    <div
                                                                        class="d-flex justify-content-start align-items-center me-3">
                                                                        <input type="checkbox" id="input-{{ $key }}"
                                                                               value="{{ $device->id }}"
                                                                               class="ui-checkbox me-2"
                                                                               @checked(in_array($device->id,$selected))
                                                                               wire:model.live="selected"
                                                                        >
                                                                        <label for="input-{{ $key }}"
                                                                               class="cursor-pointer">
                                                                            <h6 class="task_title_0">
                                                                                دستگاه {{ str($device->name)->replace('دستگاه', '') }}</h6>
                                                                            <small
                                                                                class="project_name_0 text-muted">{{ $device->serial }}</small>
                                                                        </label>
                                                                    </div>
                                                                    @if($device->lastLocation())
                                                                        <div
                                                                            class="d-flex justify-content-end align-items-center">
                                                                            <span><strong
                                                                                    class="text-success">{{ json_decode($device?->lastLocation()->device_stats)?->speed }}</strong> KM/H</span>
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr x-intersect.once="$wire.loadMore()"></tr>
                                                        @empty
                                                            <tr>
                                                                <p class="text-muted text-center">دستگاهی یافت نشد.</p>
                                                            </tr>
                                                        @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3 custom-input" x-data="dateTimeRange($refs.dateRangeInp)">
                            <div class="col-xl col-md-6">
                                <label class="form-label" for="datetime-range">انتخاب تاریخ: </label>
                                <div class="input-group flatpicker-calender">
                                    <div class="input-group flatpicker-calender" wire:ignore>
                                        <input class="form-control" id="datetime-range" type="date"
                                               wire:model="dateTimeRange"
                                               x-ref="dateRangeInp"
                                        >
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('dateTimeRange')" class="mt-1"/>
                            </div>
                            <div class="col d-flex justify-content-start align-items-center m-t-40"><a
                                    class="btn btn-primary f-w-500" type="button" wire:click="handleTrip"
                                    :class="disabled && 'disabled'">فیلتر</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body z-1 position-relative" x-data="mapComponent($refs.map)" wire:ignore>
                    <div class="map-js-height" x-ref="map" id="map"></div>

                    <div wire:loading>
                        <div class="bg-loader">
                            <div class="loader"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@assets
<!-- // Leaflet JS assets  -->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/libs/leaflet/leaflet.css') }}">
<script src="{{ asset('assets/libs/leaflet/leaflet.js') }}"></script>

<!-- // Leaflet Geoman for Geofence assets  -->
<link
    rel="stylesheet"
    href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css"
/>
<script src="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.js"></script>

<!-- // Fullscreen Map assets  -->
<link rel="stylesheet" href="{{ asset('assets/libs/leaflet/fullscreen/Control.FullScreen.css') }}">
<script src="{{ asset('assets/libs/leaflet/fullscreen/Control.FullScreen.js') }}"></script>

<!-- // dataTable for Device lists assets  -->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select.bootstrap5.css') }}">

<!-- // Date Picker assets  -->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
<script src="{{ asset('assets/js/flat-pickr/jdate.js') }}"></script>
<script src="{{ asset('assets/js/flat-pickr/flatpickr-jdate.js') }}"></script>
<script src="{{ asset('assets/js/flat-pickr/l10n/fa-jdate.js') }}"></script>

<!-- // Waypoint assets  -->
<link rel="stylesheet" href="{{ asset('assets/libs/leaflet-routing-machine/css/leaflet-routing-machine.css') }}">
<script src="{{ asset('assets/libs/leaflet-routing-machine/js/leaflet-routing-machine.js') }}"></script>


<style>
    #map {
        height: 80vh;
        z-index: 1 !important;
    }

    .route-arrow {
        font-size: 20px;
        color: red;
        text-shadow: 2px 2px 2px white;
    }

    .show-arrows-btn {
        position: absolute;
        top: 58px;
        right: 9px;
        z-index: 1000;
        padding: 10px;
        background: white;
        border: 2px solid #ccc;
        border-radius: 4px;
        cursor: pointer;
    }

    .custom-marker {
        background: none;
        border: none;
    }

    .custom-marker div {
        transition: transform 0.3s ease;
        transform-origin: center center;
    }

    .marker-popup h4 {
        margin: 0 0 8px 0;
        color: #333;
    }

    .marker-popup p {
        margin: 4px 0;
        color: #666;
    }

    .device-sidebar {
        height: 90vh;
    }
</style>
@endassets

@script
<script>
    // Map
    //------------------------------------------------------
    Alpine.data('mapComponent', (el) => ({
        map: null,
        mapCenter: [35.715298, 51.404343],
        control: null,
        markers: {},
        drownGeofences: {},
        drawnWaypoints: {},
        circleMarkers: [],

        init() {
            // Initializing The Map
            this.map = L.map(el, {
                pmIgnore: false,
                fullscreenControl: true,
            }).setView(this.mapCenter, 11);

            let layers = {
                "تصویر ماهواره ای": L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                }),
                "تصویر خیابانی گوگل": L.tileLayer('http://{s}.google.com/vt?lyrs=m&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                }),
            }

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 35,
            }).addTo(this.map);

            // Fixing Popup when zooming
            L.Popup.prototype._animateZoom = function (e) {
                if (!this._map) return;
                let pos = this._map._latLngToNewLayerPoint(this._latlng, e.zoom, e.center),
                    anchor = this._getAnchor();
                L.DomUtil.setPosition(this._container, pos.add(anchor));
            }

            L.control.layers(null, layers).addTo(this.map);

            this.map.pm.setLang("fa");

            // Livewire Events
            $wire.on('geo-fetched', (data) => {
                if (data[0].length > 0) this.showGeofences(data[0]);
            });

            // Initial Map Waypoint
            $wire.on('trips-fetched', (trips) => {
                if (trips.length > 0) {
                    this.showWaypoints(Object.values(trips[0]));
                }
            })

            // Reset
            $wire.on('geo-reset', () => this.removeGeofences())
            $wire.on('trips-reset', () => this.removeWayPoints())


            this.updateLocations($wire.deviceLocations);
            $wire.on('locationUpdated', () => this.updateLocations($wire.deviceLocations));
        },

        // Handle The Devices live location
        //-----------------------------------
        updateLocations(locations) {
            // Remove old Markers
            Object.values(this.markers).forEach(marker => {
                marker.remove();
            });
            this.markers = {};

            let bounds = L.latLngBounds();

            // Add New Markers
            Object.entries(locations).forEach(([deviceId, data]) => {
                if (!data?.lat || !data?.long) return;

                const position = [parseFloat(data.lat), parseFloat(data.long)];
                if (isNaN(position[0]) || isNaN(position[1])) return;

                const status = this.getMarkerStatus(data);
                const marker = L.marker(position, {
                    icon: this.createCustomIcon(status, data.device_stats?.['direction'])
                }).bindPopup(this.createPopupContent(data));

                this.markers[deviceId] = marker;
                marker.addTo(this.map);
                bounds.extend(position);
            });

            // Set Map View
            if ($wire.selected.length > 0) {
                const selectedLocation = locations[$wire.selected.at(-1)];
                if (selectedLocation) {
                    const position = [parseFloat(selectedLocation.lat), parseFloat(selectedLocation.long)];
                    if (!isNaN(position[0]) && !isNaN(position[1])) {
                        this.map.setView(position, 14, {animate: true, duration: 1});
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

        createCustomIcon(status = 'active', degree) {
            const markerIcons = {
                active: `<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" x="0px" y="0px" viewBox="0 0 512 512" xml:space="preserve"><g><g><path fill="#4CAF50" fill-rule="evenodd" d="M124.124,432.18l66.555-216.71L0,79.82l512,55.156L124.124,432.18z"/></g></g></svg>`,
                inactive: `<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" x="0px" y="0px" viewBox="0 0 512 512" xml:space="preserve"><g><g><path fill="#FF5722" fill-rule="evenodd" d="M124.124,432.18l66.555-216.71L0,79.82l512,55.156L124.124,432.18z"/></g></g></svg>`,
                warning: `<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" x="0px" y="0px" viewBox="0 0 512 512" xml:space="preserve"><g><g><path fill="#FFC107" fill-rule="evenodd" d="M124.124,432.18l66.555-216.71L0,79.82l512,55.156L124.124,432.18z"/></g></g></svg>`
            };


            return L.divIcon({
                html: `<div style="transform: rotate(${Number(degree || 0)}deg);">${markerIcons[status]}</div>`,
                className: 'custom-marker',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });
        },

        createPopupContent(data, distance = null) {
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
            <p style="margin: 0 !important; padding: 3px 0 3px 20px !important; white-space: nowrap; vertical-align: middle !important; text-align: right">
                <span style="margin-left: 5px"><i class="icofont icofont-street-view"></i></span>
                <a href="https://www.google.com/maps?q&layer=c&cbll=${data.lat},${data.long}" rel="nofollow noopener noreferrer" target="_blank">نمایش خیابانی 360 درجه (صفحه جدید)</a>
            </p>
            <p style="margin: 0 !important; padding: 3px 0 3px 20px !important; white-space: nowrap; vertical-align: middle !important; text-align: right">
                <span style="margin-left: 5px"><i class="icofont icofont-speed-meter"></i></span> ${Math.round(JSON.parse(data.device_stats)?.speed || 0)} کیلومتر ‌بر ساعت
            </p>
            ${distance ?
                `<p style="margin: 0 !important; padding: 3px 0 3px 20px !important; white-space: nowrap; vertical-align: middle !important; text-align: right">
                    <span style="margin-left: 5px"><i class="fa fa-solid fa-flag-checkered"></i></span> ${Math.round(distance)} کیلومتر
                </p>`
                : ''
            }
        `;
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
                        const polygon = L.polygon(latlngCoordinates, {color: color}).addTo(this.map);
                        const label = L.marker(polygon.getBounds().getCenter(), {
                            icon: L.divIcon({
                                className: 'geofence-label',
                                html: `<span class="fw-bold bg-white d-block p-1 mb-1 text-center rounded">${fence.name}</span>`,
                                iconSize: [100, 20],
                            })
                        }).addTo(this.map);

                        this.drownGeofences[fence.id] = {polygon};
                        this.drownGeofences[fence.id].label = label;

                    }
                } catch (error) {
                    console.error("Invalid geometry format:", error);
                }
            });
        },

        removeGeofences() {
            Object.values(this.drownGeofences).forEach(({polygon, label}) => {
                this.map.removeLayer(polygon);
                if (label) {
                    this.map.removeLayer(label);
                }
            });
            this.drownGeofences = {};
        },

        getColorForGeofence(geofenceId) {
            const colors = [
                "#FF5733", "#33FF57", "#3357FF", "#FF33A1", "#33FFA1", "#A133FF", "#3D27CD", "#DEF61D", "#811414", "#382F18", "#F3D24D", "#3ABA61"
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
                iconUrl: '{{ asset('assets/libs/leaflet-routing-machine/img/map-start.svg') }}',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            const endIcon = L.icon({
                iconUrl: '{{ asset('assets/libs/leaflet-routing-machine/img/map-end.svg') }}',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            this.removeWayPoints();
            this.circleMarkers = []; // Array to store circle markers

            trips.forEach((trip, i) => {
                const routeCoords = trip.map(coord => [parseFloat(coord.lat), parseFloat(coord.long)]);

                // Calculate total distance
                let totalDistance = 0;
                for (let j = 0; j < routeCoords.length - 1; j++) {
                    totalDistance += this.map.distance(routeCoords[j], routeCoords[j + 1]);
                }
                totalDistance = (totalDistance / 1000).toFixed(2);


                const polyline = L.polyline(routeCoords, {
                    color: "#F50A0AFF",
                    weight: 5,
                    opacity: 0.9
                }).addTo(this.map);

                const startMarker = L.marker(routeCoords[0], {icon: startIcon, title: 'شروع'}).addTo(this.map);
                const endMarker = L.marker(routeCoords.at(-1), {icon: endIcon, title: 'پایان'}).addTo(this.map);

                startMarker.bindPopup(this.createPopupContent(trip[0], totalDistance));
                endMarker.bindPopup(this.createPopupContent(trip.at(-1), totalDistance));

                routeCoords.forEach((coord, i) => {
                    const circle = L.circleMarker(coord, {
                        radius: 5,
                        color: "#3388ff",
                        fillOpacity: 0.5
                    }).addTo(this.map);

                    circle.on('click', (event) => {
                        const popupContent = this.createPopupContent(trip[i]);
                        L.popup()
                            .setLatLng(event.latlng)
                            .setContent(popupContent)
                            .openOn(this.map);
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

                this.drawnWaypoints[i] = polyline;
                this.drawnWaypoints[i].markers = {startMarker, endMarker};
            });

            console.log(this.drawnWaypoints);
        },

        removeWayPoints() {
            Object.values(this.drawnWaypoints).forEach((route) => {
                if (route) this.map.removeLayer(route);

                if (route.markers) {
                    Object.values(route.markers).forEach((marker) => {
                        if (marker) this.map.removeLayer(marker);
                    });
                }
            });

            this.circleMarkers.forEach((circle) => {
                if (circle) this.map.removeLayer(circle);
            });

            this.drawnWaypoints = {};
            this.circleMarkers = [];
        },


        showWayPointWithRouteMachineLibrary(trips) {
            const startIcon = L.icon({
                iconUrl: '{{ asset('assets/libs/leaflet-routing-machine/img/map-start.svg') }}',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            const endIcon = L.icon({
                iconUrl: '{{ asset('assets/libs/leaflet-routing-machine/img/map-end.svg') }}',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            this.removeWayPoints();
            this.circleMarkers = []; // Array to store circle markers

            trips.forEach((trip, i) => {
                const control = L.Routing.control({
                    waypoints: [
                        L.latLng(parseFloat(trip[0].lat), parseFloat(trip[0].long)),
                        L.latLng(parseFloat(trip.at(-1).lat), parseFloat(trip.at(-1).long))
                    ],
                    routeWhileDragging: false,
                    addWaypoints: false,
                    draggableWaypoints: false,
                    createMarker: function (i, waypoint, n) {
                        let marker_icon = (i === 0) ? startIcon : endIcon;
                        let marker_title = (i === 0) ? 'شروع' : 'پایان';
                        return L.marker(waypoint.latLng, {
                            icon: marker_icon,
                            title: marker_title,
                            draggable: false
                        });
                    },
                    lineOptions: {
                        styles: [{
                            color: "#F50A0AFF",
                            weight: 5,
                            opacity: 0.9
                        }]
                    }
                }).addTo(this.map);


                control.on('routesfound', (e) => {
                    const route = e.routes[0];
                    console.log(route.coordinates)
                    const totalDistance = route.summary.totalDistance;
                    // route.coordinates.forEach
                    trip.forEach((coord) => {
                        // Create a circle marker on each coordinate of the route
                        const circle = L.circleMarker([coord.lat, coord.long], {
                            radius: 5,
                            color: "#3388ff",
                            fillOpacity: 0.5
                        }).addTo(this.map);
                        console.log(trip)

                        // Add click event for displaying trip info
                        circle.on('click', (event) => {
                            const popupContent = this.createPopupContent(trip);
                            L.popup()
                                .setLatLng(event.latlng)
                                .setContent(popupContent)
                                .openOn(this.map);
                        });

                        this.circleMarkers.push(circle);
                    });

                    // Add zoom event to show/hide circles based on zoom level
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
                });

                this.drawnWaypoints[i] = control;
            });
        },

        removeWayPointWithRouteMachineLibrary() {
            Object.values(this.drawnWaypoints).forEach((route) => {
                this.map.removeControl(route);
            });
            this.circleMarkers.forEach((circle) => this.map.removeLayer(circle))

            this.drawnWaypoints = {};
            this.circleMarkers = [];
        }
    }))
    ;

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
                placeholder: this.placeholder
            });
        }
    }));
</script>
@endscript

