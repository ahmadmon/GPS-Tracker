@extends('01-layouts.master')
@section('title', 'داشبورد')


@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        #map {
            height: 80vh;
            z-index: 1 !important;
        }
    </style>
@endpush


@section('content')
<div class="pt-4">
    <div id="map"></div>
</div>
@endsection

@push('scripts')
    <script>
        let map = L.map('map').setView([35.67085175325499,51.340655979034864], 17);
        var marker = L.marker([35.67085175325499,51.340655979034864]).addTo(map);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 35,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
    </script>
@endpush
