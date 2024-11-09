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
                                                                    <div>
                                                                        <h6 class="task_title_0">
                                                                            دستگاه {{ str($device->name)->replace('دستگاه', '') }}</h6>
                                                                        <small
                                                                            class="project_name_0 text-muted">{{ $device->serial }}</small>
                                                                    </div>
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
                                        <option value="{{ $key }}">{{ jalaliDate($trip, format: '%d %B %Y H:i') }}</option>
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

<style>
    #map {
        height: 80vh;
        z-index: 1 !important;
    }
</style>
@endassets

@script
<script>
    Alpine.data('mapComponent', (el) => ({

        init() {
            const map = L.map(el, {
                pmIgnore: false,
                fullscreenControl: true,
            }).setView([35.715298, 51.404343], 11);

            let layers = {
                "تصویر ماهواره ای": L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {subdomains: ['mt0', 'mt1', 'mt2', 'mt3']}),
                "تصویر خیابانی گوگل": L.tileLayer('http://{s}.google.com/vt?lyrs=m&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                }),
            }


            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 35,
            }).addTo(map);


            L.control.layers(null, layers).addTo(map);

            map.pm.setLang("fa");


            // if (initialGeometry) {
            //     try {
            //         const geometryData = JSON.parse(initialGeometry);
            //         if (geometryData && geometryData.latlng) {
            //             const latlngCoordinates = geometryData.latlng.map(coord => [coord[1], coord[0]]);
            //             const polygon = L.polygon(latlngCoordinates).addTo(map);
            //             map.fitBounds(polygon.getBounds());
            //         }
            //     } catch (error) {
            //         console.error("Invalid geometry format:", error);
            //     }
            // }
        }
    }));
</script>
@endscript

