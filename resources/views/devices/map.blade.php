@extends('01-layouts.master')

@section('title', 'نمایش موقعیت دستگاه')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/leaflet.css') }}">
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
                        <li class="breadcrumb-item">
                            <a href="{{ route('device.index') }}">
                                لیست دستگاه ها
                            </a>
                        </li>
                        <li class="breadcrumb-item dana">نمایش موقعیت در نقشه</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <div class="container-fluid">
        <x-partials.alert.success-alert/>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h5>اخرین موقعیت دستگاه {{ $device->name }}</h5>
                        <span>موقعیت دستگاه به صورت آنلاین هر ۳۰ ثانیه تغییر میکند.</span>
                    </div>
                    <div class="card-body z-1" x-data="mapComponent($refs.mapBox)">
                        <div class="map-js-height" x-ref="mapBox" data-device_id="{{ $device->id }}"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/map-js/leaflet.js') }}"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('mapComponent', (el) => ({
                deviceId: el.dataset.device_id,
                data: null,
                map: null,
                marker: null,

                async getLocation() {
                    try {
                        let res = await fetch(`/device/get-location/${this.deviceId}`);
                        this.data = await res.json();


                        if (this.data.status === 'success') {
                            let position = this.data.data;
                            let latLng = [position.lat, position.lng];
                            let popup = `
                                <p style="margin: 0 !important; padding: 3px 0 3px 20px !important; white-space: nowrap; vertical-align: middle !important; text-align: right"><span style="margin-left: 5px"><i class="fa fa-car"></i></span>{{ $device->vehicle?->name ?? 'نامشخص' }} </p>
                                <p style="margin: 0 !important; padding: 3px 0 3px 20px !important; white-space: nowrap; vertical-align: middle !important; text-align: right"><span style="margin-left: 5px"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock" style="width: 15px;height: 15px"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg></span>${position.name} </p>
                                <p style="margin: 0 !important; padding: 3px 0 3px 20px !important; white-space: nowrap; vertical-align: middle !important; text-align: right"><span style="margin-left: 5px"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin" style="width: 15px;height: 15px"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg></span> <a href="https://maps.google.com/?q=${position.lat},${position.lng}" rel="nofollow noopener noreferrer" target="_blank">${parseFloat(position.lat).toFixed(4)},${parseFloat(position.lng).toFixed(4)}</a></p>
                                <p style="margin: 0 !important; padding: 3px 0 3px 20px !important; white-space: nowrap; vertical-align: middle !important; text-align: right"><span style="margin-left: 5px"><i class="icofont icofont-speed-meter"></i></span> ${Math.round(parseInt(position.speed))} کیلومتر ‌بر ساعت </p>
                               `

                            if (this.marker) {
                                this.marker.setLatLng(latLng).bindPopup(popup);
                            } else {
                                this.marker = L.marker(latLng).bindPopup(popup).addTo(this.map);
                            }

                            this.map.setView(latLng, 15);
                        }
                    } catch (error) {
                        console.error(`Error getting location: ${error}`);
                    }
                },

                init() {
                    let lastLatLng = [{{ (float)$lastLocation->lat }}, {{ (float)$lastLocation->long }}];
                    this.map = L.map(el).setView(lastLatLng, 15);
                    const markerIcon = L.icon({
                        iconUrl: 'https://api.geoapify.com/v1/icon/?type=awesome&color=%2318b61c&size=x-large&icon=car&apiKey=90dbdf8d514f4ebba9bf57aac83a8bc4',
                        iconSize: [31, 46],
                        iconAnchor: [15.5, 42],
                        popupAnchor: [0, -45]
                    })
                    let popup = `
                                <p style="margin: 0 !important; padding: 3px 0 3px 20px !important; white-space: nowrap; vertical-align: middle !important; text-align: right"><span style="margin-left: 5px"><i class="fa fa-car"></i></span>{{ $device->vehicle?->name ?? 'نامشخص' }} </p>
                                <p style="margin: 0 !important; padding: 3px 0 3px 20px !important; white-space: nowrap; vertical-align: middle !important; text-align: right"><span style="margin-left: 5px"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock" style="width: 15px;height: 15px"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg></span>{{ $lastLocation->name }} </p>
                                <p style="margin: 0 !important; padding: 3px 0 3px 20px !important; white-space: nowrap; vertical-align: middle !important; text-align: right"><span style="margin-left: 5px"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin" style="width: 15px;height: 15px"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg></span> <a href="https://maps.google.com/?q={{ $lastLocation->lat }},{{$lastLocation->long}}" rel="nofollow noopener noreferrer" target="_blank">{{ number_format($lastLocation->lat,4) }},{{ number_format($lastLocation->long,4) }}</a></p>
                                <p style="margin: 0 !important; padding: 3px 0 3px 20px !important; white-space: nowrap; vertical-align: middle !important; text-align: right"><span style="margin-left: 5px"><i class="icofont icofont-speed-meter"></i></span> {{ number_format($lastLocation->device_stats['data']['speed']) }} کیلومتر ‌بر ساعت </p>
                               `

                    this.marker = L.marker(lastLatLng, {
                        icon: markerIcon,
                        riseOnHover: true,
                        autoPan: true,
                        draggable: false
                    }).bindPopup(popup)
                        .addTo(this.map);

                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 35,
                        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    }).addTo(this.map);


                    setInterval(() => {
                        this.getLocation()
                    }, 30000)
                }

            }))
        })
    </script>
@endpush
