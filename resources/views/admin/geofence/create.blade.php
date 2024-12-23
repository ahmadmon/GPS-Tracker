@extends('01-layouts.master')

@section('title', 'ایجاد حصار جدید')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/libs/leaflet/leaflet.css') }}">
    <script src="{{ asset('assets/libs/leaflet/leaflet.js') }}"></script>

    <link
        rel="stylesheet"
        href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css"
    />
    <script src="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.js"></script>
    <link rel="stylesheet" href="{{ asset('assets/libs/leaflet/fullscreen/Control.FullScreen.css') }}">

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
                        <li class="breadcrumb-item dana">ایجاد حصار جدید</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <form action="{{ route('geofence.store') }}" method="POST" class="row" autocomplete="off">
        @csrf
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
                            <input class="form-control" id="name" name="name" value="{{ old('name') }}" type="text">
                            <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">توضیحات</label>
                            <textarea id="description" name="description" class="form-control"
                                      rows="3">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2"/>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="status">وضعیت
                                <sup class="text-danger">*</sup>
                            </label>
                            <select class="form-select" name="status" id="status">
                                <option value="0" @selected(old('status') == 0)>غیر فعال</option>
                                <option value="1" selected @selected(old('status') == 1)>فعال</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2"/>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="type">نوع حصار
                                <sup class="text-danger">*</sup>
                            </label>
                            <select class="form-select" name="type" id="type">
                                <option value="0" selected @selected(old('type') == 0)>خروج از حصار</option>
                                <option value="1" @selected(old('type') == 1)>ورود به حصار</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2"/>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="user_id">مختص به دستگاه
                                <sup class="text-danger">*</sup>
                            </label>
                            @php
                                if($role === 'user'){
                                    $options = $devices->mapWithKeys(fn($item) => [$item->id => implode(' - ', [$item->name , $item?->serial])])->toArray();
                                    $value = count($devices) === 1 ? $devices->first()->id : null;

                                }else{
                                    $options = $devices->mapWithKeys(fn($item) => [$item->id => implode(' - ', [$item->name , $item?->user->name])])->toArray();
                                    $value = null;
                                }
                            @endphp
                            <x-partials.alpine.input.select-option name="device_id" :value="$value"
                            :$options/>
                            <x-input-error :messages="$errors->get('device_id')" class="mt-2"/>
                        </div>

                        <hr class="mt-2">

                        <div x-data="{ show: @js(old('time_restriction', false)) }">
                            <div class="d-flex mb-3">
                                <label class="col-form-label m-r-10">اعمال محدودیت زمانی</label>
                                <div class="flex-grow-1 text-end icon-state">
                                    <label class="switch">
                                        <input type="checkbox" @click="show = !show" name="time_restriction" @checked(old('time_restriction',false))  ><span class="switch-state"></span>
                                    </label>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('time_restriction')" class="mt-2"/>
                            <small class="text-muted">در این بخش، دو فیلد زمان شروع و زمان پایان داریم که مشخص می‌کنند این حصار جغرافیایی در چه بازه زمانی فعال باشد.</small>
                            <div x-show="show" x-cloak>
                                @php $now = now()->format('H:i') @endphp

                                <div class="mb-3">
                                    <label class="form-label" for="start_time">زمان شروع</label>
                                    <div class="input-group" x-data x-init="
                                  flatpickr($refs.startTimeEl, {
                                    enableTime: true,
                                    noCalendar: true,
                                    defaultDate: '{{ old('start_time', $now) }}',
                                    dateFormat: 'H:i',
                                    time_24hr: true
                            });
                            ">
                                        <input class="form-control text-center" id="start_time" name="start_time"
                                               x-ref="startTimeEl"
                                               type="time"
                                               value="{{ old('start_time') }}"
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
                                            defaultDate: '{{ old('end_time', $now) }}',
                                            dateFormat: 'H:i',
                                            time_24hr: true
                                          });
                            ">
                                        <input class="form-control text-center" id="end_time" name="end_time"
                                               x-ref="endTimeEl" type="time"
                                               value="{{ old('end_time') }}"
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
                        <h4 class="card-title mb-0">رسم حصار
                            <sup class="text-danger">*</sup>
                        </h4>
                        <p class="text-muted ms-1">- تنها امکان رسم یک حصار وجود دارد.</p>
                    </div>
                    <div class="card-body z-1" x-data="mapComponent($refs.map, '{{ old('geometry') }}')">
                        <x-input-error :messages="$errors->get('geometry')" class="mt-2 text-center"/>
                        <div class="map-js-height" x-ref="map"></div>
                        <input aria-label="رسم حصار" type="text" value="{{ old('geometry') }}" class="d-none"
                               name="geometry" x-ref="geometryInput">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 mt-2">
            <button class="btn btn-primary" type="submit">ایــــجاد</button>
        </div>
    </form>

@endsection

@push('scripts')
    <script src="{{ asset('assets/libs/leaflet/fullscreen/Control.FullScreen.js') }}"></script>
    <script src="{{ asset('assets/js/flat-pickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/libs/leaflet/leaflet-map-layers.js') }}"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('mapComponent', (el, initialGeometry) => ({

                init() {

                    const map = L.map(el, {
                        pmIgnore: false,
                        fullscreenControl: true,
                    }).setView([35.715298, 51.404343], 11);

                    OSMBase.addTo(map);
                    L.control.layers(baseMaps, overlayMaps, {position: 'topleft', collapsed: true}).addTo(map);

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
                    });

                    map.pm.enableDraw("Polygon", {
                        snappable: true,
                        snapDistance: 20,
                        tooltips: true,
                    });


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
