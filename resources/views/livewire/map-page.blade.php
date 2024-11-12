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
        <div class="col-12">

        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="email-right-aside bookmark-tabcontent">
                    <div class="card email-body radius-left">
                        <div class="ps-0">
                            <div class="tab-content">
                                <div class="tab-pane fade active show" id="pills-created" role="tabpanel">
                                    <div class="card mb-0">
                                        <div class="card-header d-flex">
                                            <h5>دستگاه ها</h5>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="col-md-12">
                                                <input class="form-control rounded-0" id="search" type="text"
                                                       placeholder="جستجو بر اساس نام یا شناسه..." aria-label="جسنجو"
                                                       wire:model.live.debounce.850ms="search">
                                            </div>
                                            <div class="taskadd">
                                                <div class="table-responsive custom-scrollbar">
                                                    <table class="table">
                                                        <tbody>
                                                        @forelse($devices as $key => $device)
                                                            <tr>
                                                                <td class="w-100 d-flex justify-content-start align-items-center">
                                                                    <input type="checkbox" id="input-{{ $key }}"
                                                                           value="{{ $device->id }}"
                                                                           class="ui-checkbox me-2"
                                                                           @checked($loop->first) wire:model.live="selected">
                                                                    <label for="input-{{ $key }}"
                                                                           class="cursor-pointer">
                                                                        <h6 class="task_title_0">
                                                                            دستگاه {{ str($device->name)->replace('دستگاه', '') }}</h6>
                                                                        <small
                                                                            class="project_name_0 text-muted">{{ $device->serial }}</small>
                                                                    </label>
                                                                </td>
                                                            </tr>
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
                        <div class="row g-3 custom-input">
                            <div class="col-xl col-md-6">
                                <label class="form-label" for="datetime-local">انتخاب تاریخ: </label>
                                <div class="input-group flatpicker-calender">
                                    <div class="input-group flatpicker-calender">
                                        <input class="form-control" id="range-date" type="date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">انتخاب سفر دستگاه S20</label>
                                <select class="form-select">
                                    <option value="" @selected(empty($selected))>ابتدا دستگاه را انتخاب کنید...</option>
                                    @forelse($trips as $key => $trip)
                                        <option
                                            value="{{ $key }}">{{ jalaliDate($trip, format: '%d %B %Y H:i') }}</option>
                                    @empty
                                        <option disabled selected>سفری برای این دستگاه یافت نشد.</option>
                                    @endforelse
                                </select>
                            </div>
                            <div class="col d-flex justify-content-start align-items-center m-t-40"><a
                                    class="btn btn-primary f-w-500" href="#!">فیلتر</a></div>
                        </div>
                    </div>
                </div>

                <div class="card-body z-1" x-data="mapComponent($refs.map)" wire:ignore>
                    <div class="map-js-height" x-ref="map" id="map"></div>
                </div>
            </div>
        </div>
    </div>

</div>

@assets
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<link
    rel="stylesheet"
    href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css"
/>
<script src="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.js"></script>
<link rel="stylesheet" href="{{ asset('assets/js/leaflet/Control.FullScreen.css') }}">

<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select.bootstrap5.css') }}">


<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
<script src="{{ asset('assets/js/leaflet/Control.FullScreen.js') }}"></script>
<script src="{{ asset('assets/js/flat-pickr/flatpickr.js') }}"></script>

<link rel="stylesheet" href="{{ asset('assets/libs/leaflet-routing-machine/css/leaflet-routing-machine.css') }}"></link>
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


</style>
@endassets

@script
<script>
    Alpine.data('mapComponent', (el) => ({
        arrows: [],
        isArrowVisible: false,
        map: null,
        mapCenter: [35.715298, 51.404343],
        control: null,
        markers: {},

        init() {
            this.map = L.map(el, {
                pmIgnore: false,
                fullscreenControl: true,
            }).setView(this.mapCenter, 11);

            let layers = {
                "تصویر ماهواره ای": L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {subdomains: ['mt0', 'mt1', 'mt2', 'mt3']}),
                "تصویر خیابانی گوگل": L.tileLayer('http://{s}.google.com/vt?lyrs=m&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                }),
            }


            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 35,
            }).addTo(this.map);


            L.control.layers(null, layers).addTo(this.map);

            this.map.pm.setLang("fa");


            // Initial Map Waypoint
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

            this.control = L.Routing.control({
                waypoints: [
                    L.latLng(35.72446125156188, 51.43498545935407),
                    L.latLng(35.70130758910315, 51.41301462126029)
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
                        color: 'blue',
                        weight: 3,
                        opacity: 0.7
                    }]
                }
            })

            // Update Live Location
            this.updateLocations($wire.deviceLocations);
            $wire.on('locationUpdated', () => this.updateLocations($wire.deviceLocations));
            setInterval(() => {
                $wire.refreshMap();

                this.updateLocations($wire.deviceLocations);
            }, 10000)

            // let arrowButton = L.DomUtil.create('button', 'show-arrows-btn');
            // arrowButton.innerHTML = 'نمایش جهت مسیر';
            // document.querySelector('#map').appendChild(arrowButton);
            //
            // const self = this;
            // arrowButton.addEventListener('click', function () {
            //     self.isArrowVisible = !self.isArrowVisible;
            //
            //     if (self.isArrowVisible) {
            //         self.control.getRouter().route(self.control.getWaypoints(), function (error, routes) {
            //             if (!error && routes && routes[0]) {
            //                 self.addArrowsToRoute(routes[0]);
            //                 arrowButton.innerHTML = 'مخفی کردن جهت مسیر';
            //             }
            //         });
            //     } else {
            //         if (self.arrows.length) {
            //             self.arrows.forEach(arrow => self.map.removeLayer(arrow));
            //             self.arrows = [];
            //         }
            //         arrowButton.innerHTML = 'نمایش جهت مسیر';
            //     }
            // });
        },

        updateLocations(locations) {
            //remove Markers
            Object.keys(this.markers).forEach(deviceId => {
                if (!locations[deviceId]) {
                    this.map.removeLayer(this.markers[deviceId]);
                    delete this.markers[deviceId];
                }
            });

            // add markers
            Object.entries(locations).forEach(([deviceId, data]) => {
                const position = [parseFloat(data.lat), parseFloat(data.long)];
                const status = this.getMarkerStatus(data);

                if (this.markers[deviceId]) {
                    this.markers[deviceId].setLatLng(position);
                    this.markers[deviceId].setIcon(this.createCustomIcon(status, data.device_stats['direction']));

                } else {
                    let popup = `
                                <p style="margin: 0 !important; padding: 3px 0 3px 20px !important; white-space: nowrap; vertical-align: middle !important; text-align: right"><span style="margin-left: 5px"><i class="fa fa-car"></i></span>${data.vehicle?.name || 'نامشخص'} </p>
                                <p style="margin: 0 !important; padding: 3px 0 3px 20px !important; white-space: nowrap; vertical-align: middle !important; text-align: right"><span style="margin-left: 5px"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock" style="width: 15px;height: 15px"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg></span>${data.name} </p>
                                <p style="margin: 0 !important; padding: 3px 0 3px 20px !important; white-space: nowrap; vertical-align: middle !important; text-align: right"><span style="margin-left: 5px"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin" style="width: 15px;height: 15px"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg></span> <a href="https://maps.google.com/?q=${data.lat},${data.long}" rel="nofollow noopener noreferrer" target="_blank">${parseFloat(data.lat).toFixed()},${parseFloat(data.long).toFixed(4)}</a></p>
                                <p style="margin: 0 !important; padding: 3px 0 3px 20px !important; white-space: nowrap; vertical-align: middle !important; text-align: right"><span style="margin-left: 5px"><i class="icofont icofont-speed-meter"></i></span> ${Math.round(parseFloat(data.device_stats['speed'] || 0))} کیلومتر ‌بر ساعت </p>
                               `
                    this.markers[deviceId] = L.marker(position, {
                        icon: this.createCustomIcon(status, data.device_stats['direction'])
                    })
                        .bindPopup(popup)
                        .addTo(this.map);
                }

                this.mapCenter = position;
                this.map.setView(this.mapCenter, 14);
            });
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

        getMarkerStatus(data) {
            const lastUpdate = new Date(data.created_at);
            const now = new Date();
            const diffMinutes = Math.floor((now - lastUpdate) / 1000 / 60);

            if (diffMinutes < 5) return 'active';
            if (diffMinutes < 15) return 'warning';
            return 'inactive';
        },

        // addArrowsToRoute(route) {
        //     this.arrows.forEach(arrow => this.map.removeLayer(arrow));
        //     this.arrows = [];
        //
        //     if (!route || !route.coordinates) return;
        //
        //     const coordinates = route.coordinates;
        //
        //     for (let i = 0; i < coordinates.length - 1; i += Math.ceil(coordinates.length / 10)) {
        //         const start = coordinates[i];
        //         const end = coordinates[i + 1];
        //         if (!end) continue;
        //
        //         const midPoint = L.latLng(
        //             (start.lat + end.lat) / 2,
        //             (start.lng + end.lng) / 2
        //         );
        //
        //         const angle = Math.atan2(end.lat - start.lat, end.lng - start.lng) * 180 / Math.PI;
        //
        //         const arrow = L.divIcon({
        //             className: 'route-arrow',
        //             html: '→',
        //             iconSize: [20, 20],
        //             iconAnchor: [10, 10]
        //         });
        //
        //         const marker = L.marker(midPoint, {
        //             icon: arrow,
        //             rotationAngle: angle
        //         }).addTo(this.map);
        //
        //         const arrowElement = marker.getElement();
        //         if (arrowElement) {
        //             arrowElement.style.transform += ` rotate(${angle}deg)`;
        //         }
        //
        //         this.arrows.push(marker);
        //     }
        // }
    }));
</script>
@endscript

