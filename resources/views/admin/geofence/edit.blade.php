@extends('01-layouts.master')

@section('title', 'ویرایش حصار')

@push('styles')
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

    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css"/>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">

@endpush

@section('content')

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
                        <li class="breadcrumb-item dana">
                            <a href="{{ route('geofence.index') }}">
                                لیست حصارهای جغرافیایی
                            </a>
                        </li>
                        <li class="breadcrumb-item dana">ویرایش حصار</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <form action="{{ route('geofence.update', $geofence->id) }}" method="POST" class="row">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">اطلاعات کلی</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="name">نام حصار
                                <sup class="text-danger">*</sup>
                            </label>
                            <input class="form-control" id="name" name="name" value="{{ old('name', $geofence->name) }}"
                                   type="text">
                            <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">توضیحات</label>
                            <textarea id="description" name="description" class="form-control"
                                      rows="3">{{ old('description', $geofence->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')"
                                           class="mt-2"/>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="status">وضعیت
                                <sup class="text-danger">*</sup>
                            </label>
                            <select class="form-select" name="status" id="status">
                                <option value="0" @selected(old('status', $geofence->status) == 0)>غیر فعال</option>
                                <option value="1" selected @selected(old('status', $geofence->status) == 1)>فعال
                                </option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2"/>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="type">نوع حصار
                                <sup class="text-danger">*</sup>
                            </label>

                            <select class="form-select" name="type" id="type">
                                <option value="0" @selected(old('type', $geofence->type) == 0)>خروج از حصار
                                </option>
                                <option value="1" @selected(old('type', $geofence->type) == 1)>ورود به حصار</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2"/>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="user_id">مختص به دستگاه
                                <sup class="text-danger">*</sup>
                            </label>
                            <x-partials.alpine.input.select-option name="device_id" :value="$geofence->device_id"
                                                                   :options="$devices->pluck('name' , 'id')->toArray()"/>
                            <x-input-error :messages="$errors->get('device_id')" class="mt-2"/>
                        </div>

                        <hr class="mt-2">

                        <div x-data="{ show: @js(old('time_restriction', false)) }"
                             x-init="if(@js(isset($geofence->start_time))){ show = true }">
                            <div class="d-flex mb-3">
                                <label class="col-form-label m-r-10">اعمال محدودیت زمانی</label>
                                <div class="flex-grow-1 text-end icon-state">
                                    <label class="switch">
                                        <input type="checkbox" @click="show = !show"
                                               name="time_restriction" @checked(old('time_restriction',false) || isset($geofence->start_time)) ><span
                                            class="switch-state"></span>
                                    </label>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('time_restriction')" class="mt-2"/>
                            <small class="text-muted">در این بخش، دو فیلد زمان شروع و زمان پایان داریم که مشخص می‌کنند
                                این حصار جغرافیایی در چه بازه زمانی فعال باشد.</small>
                            <div x-show="show" x-cloak>
                                @php $now = now()->format('H:i') @endphp

                                <div class="mb-3">
                                    <label class="form-label" for="start_time">زمان شروع</label>
                                    <div class="input-group" x-data x-init="
                                  flatpickr($refs.startTimeEl, {
                                    enableTime: true,
                                    noCalendar: true,
                                    defaultDate: '{{ old('start_time', $geofence->start_time) }}',
                                    dateFormat: 'H:i',
                                    time_24hr: true
                            });
                            ">
                                        <input class="form-control text-center" id="start_time" name="start_time"
                                               x-ref="startTimeEl"
                                               type="time"
                                               value="{{ old('start_time', $geofence->start_time) }}"
                                               placeholder="{{ $now }}">
                                    </div>
                                    <x-input-error :messages="$errors->get('start-time')" class="mt-2"/>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="end_time">زمان پایان</label>
                                    <div class="input-group" x-data x-init="
                                          flatpickr($refs.endTimeEl, {
                                            enableTime: true,
                                            noCalendar: true,
                                            defaultDate: '{{ old('end_time', $geofence->end_time) }}',
                                            dateFormat: 'H:i',
                                            time_24hr: true
                                          });
                            ">
                                        <input class="form-control text-center" id="end_time" name="end_time"
                                               x-ref="endTimeEl" type="time"
                                               value="{{ old('end_time', $geofence->end_time) }}"
                                               placeholder="{{ $now}}">
                                    </div>
                                    <x-input-error :messages="$errors->get('end_time')" class="mt-2"/>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">رسم حصار</h4>
                    </div>
                    @php
                        $geometry = [
                        'shape' => $geofence->shape,
                        'latlng' => $geofence->points
                        ]
                    @endphp
                    <div class="card-body z-1"
                         x-data="mapComponent($refs.map, '{{ old('geometry', json_encode($geometry)) }}')">
                        <x-input-error :messages="$errors->get('geometry')" class="mt-2 text-center"/>
                        <div class="map-js-height" x-ref="map"></div>
                        <input aria-label="رسم حصار" type="text" value="{{ old('geometry', json_encode($geometry)) }}"
                               class="d-none"
                               name="geometry" x-ref="geometryInput">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 mt-2">
            <button class="btn btn-primary" type="submit">ویرایش</button>
        </div>
    </form>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/leaflet/Control.FullScreen.js') }}"></script>
    <script src="{{ asset('assets/js/flat-pickr/flatpickr.js') }}"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('mapComponent', (el, initialGeometry) => ({

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
                    // add Leaflet-Geoman controls with some options to the map
                    map.pm.addControls({
                        position: 'topright',
                        drawMarker: false,
                        drawCircleMarker: false,
                        drawRectangle: false,
                        drawPolyline: false,
                        drawText: false,
                        drawCircle: false,
                        oneBlock: true,
                        drawPolygon: !initialGeometry
                    });

                    if (!initialGeometry) {
                        map.pm.enableDraw("Polygon", {
                            snappable: true,
                            snapDistance: 20,
                            tooltips: true,
                        });
                    }


                    map.on('pm:create', (e) => {
                        const shape = e.shape;
                        const layer = e.layer.toGeoJSON();
                        let geometry = {
                            'shape': shape,
                            'latlng': layer.geometry.coordinates[0]
                        };

                        this.$refs.geometryInput.value = JSON.stringify(geometry);
                    })

                    L.Control.geocoder({
                        defaultMarkGeocode: false,
                        placeholder: 'آدرس را وارد کنید...',
                        geocoder: L.Control.Geocoder.nominatim({
                            geocodingQueryParams: {
                                countrycodes: 'IR'
                            }
                        })
                    }).on('markgeocode', function (e) {
                        const coords = [e.geocode.center.lat, e.geocode.center.lng];
                        map.setView(coords, 15);
                    })
                        .addTo(map);

                    if (initialGeometry) {
                        try {
                            const geometryData = JSON.parse(initialGeometry);
                            if (geometryData && geometryData.latlng) {
                                const latlngCoordinates = geometryData.latlng.map(coord => [coord[1], coord[0]]);
                                const polygon = L.polygon(latlngCoordinates).addTo(map);
                                map.fitBounds(polygon.getBounds());
                            }
                        } catch (error) {
                            console.error("Invalid geometry format:", error);
                        }
                    }
                }
            }));
        })
    </script>
@endpush
