@extends('01-layouts.master')
@section('title', 'داشبورد')


@push('styles')

@endpush


@section('content')
    <div class="container-fluid pt-3">
        <div class="row widget-grid">
            <!-- Clock and Hadis -->
            <div class="col-xxl-4 col-sm-6 box-col-6">
                <div class="card profile-box">
                    <div class="card-body">
                        <div class="media media-wrapper justify-content-between" x-init="startTime()"
                             style="height: 177px">
                            <div class="media-body">
                                <div class="greeting-user">
                                    <h4 class="f-w-600">{{ auth()->user()->name }} خوش آمدید!</h4>

                                    <div class="w-75">
                                        @if($hadis)
                                            <p class="mb-0 mt-2">حدیث روز:</p>
                                            <strong
                                                class="d-block">- {{ json_decode($hadis->value, true)['person'] ?? '' }}
                                                :</strong>
                                            <q>{{ json_decode($hadis->value, true)['text'] ?? '' }}</q>
                                            <small
                                                class="d-block opacity-50">{{ json_decode($hadis->value, true)['source'] ?? '' }}</small>
                                        @else
                                            <p class="mb-0 mt-2">امیدوارم روز خوبی داشته باشی...</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="clockbox">
                                    <svg id="clock" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600">
                                        <g id="face">
                                            <circle class="circle" cx="300" cy="300" r="253.9"></circle>
                                            <path class="hour-marks"
                                                  d="M300.5 94V61M506 300.5h32M300.5 506v33M94 300.5H60M411.3 107.8l7.9-13.8M493 190.2l13-7.4M492.1 411.4l16.5 9.5M411 492.3l8.9 15.3M189 492.3l-9.2 15.9M107.7 411L93 419.5M107.5 189.3l-17.1-9.9M188.1 108.2l-9-15.6"></path>
                                            <circle class="mid-circle" cx="300" cy="300" r="16.2"></circle>
                                        </g>
                                        <g id="hour">
                                            <path class="hour-hand" d="M300.5 298V142"></path>
                                            <circle class="sizing-box" cx="300" cy="300" r="253.9"></circle>
                                        </g>
                                        <g id="minute">
                                            <path class="minute-hand" d="M300.5 298V67"></path>
                                            <circle class="sizing-box" cx="300" cy="300" r="253.9"></circle>
                                        </g>
                                        <g id="second">
                                            <path class="second-hand" d="M300.5 350V55"></path>
                                            <circle class="sizing-box" cx="300" cy="300" r="253.9"></circle>
                                        </g>
                                    </svg>
                                </div>
                                <div class="badge f-10 p-0 dana" id="txt"></div>
                            </div>
                        </div>
                        <div class="cartoon"><img class="img-fluid" width="175"
                                                  src="{{ asset('assets/images/dashboard/police-investigation.webp') }}"
                                                  alt=""></div>
                    </div>
                </div>
            </div>
            <!-- Users and Devices count -->
            <div class="col-xxl-auto col-xl-3 col-sm-6 box-col-6">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card widget-1">
                            <div class="card-body">
                                <div class="widget-content">
                                    <div class="widget-round primary">
                                        <div class="bg-round">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                                                 viewBox="0 0 24 24">
                                                <g fill="none" stroke="#7366FF" stroke-width="1.5">
                                                    <path stroke-linecap="round"
                                                          d="M6.009 8c.036-2.48.22-3.885 1.163-4.828C8.343 2 10.229 2 14 2s5.657 0 6.828 1.172S22 6.229 22 10v4c0 3.771 0 5.657-1.172 6.828S17.772 22 14 22h-2"/>
                                                    <path
                                                        d="M2 14.5c0-1.405 0-2.107.337-2.611a2 2 0 0 1 .552-.552C3.393 11 4.096 11 5.5 11s2.107 0 2.611.337a2 2 0 0 1 .552.552C9 12.393 9 13.096 9 14.5v4c0 1.404 0 2.107-.337 2.611a2 2 0 0 1-.552.552C7.607 22 6.904 22 5.5 22s-2.107 0-2.611-.337a2 2 0 0 1-.552-.552C2 20.607 2 19.904 2 18.5z"/>
                                                    <path stroke-linecap="round" d="M17 19h-5"/>
                                                </g>
                                            </svg>
                                            <svg class="half-circle svg-fill">
                                                <use href="{{ asset('assets/svg/icon-sprite.svg#halfcircle') }}"></use>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4><span class="counter"
                                                  data-target="{{ $entities->get('devices_count') }}">{{ priceFormat($entities->get('devices_count')) ?? 0 }}</span>
                                        </h4><span
                                            class="f-light">کل دستگاه ها</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="card widget-1">
                            <div class="card-body">
                                <div class="widget-content">
                                    <div class="widget-round success">
                                        <div class="bg-round">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                                                 viewBox="0 0 36 36">
                                                <path fill="#54BA4A"
                                                      d="M17.9 17.3c2.7 0 4.8-2.2 4.8-4.9s-2.2-4.8-4.9-4.8S13 9.8 13 12.4c0 2.7 2.2 4.9 4.9 4.9m-.1-7.7q.15 0 0 0c1.6 0 2.9 1.3 2.9 2.9s-1.3 2.8-2.9 2.8S15 14 15 12.5c0-1.6 1.3-2.9 2.8-2.9"
                                                      class="clr-i-outline clr-i-outline-path-1"/>
                                                <path fill="#54BA4A"
                                                      d="M32.7 16.7c-1.9-1.7-4.4-2.6-7-2.5h-.8q-.3 1.2-.9 2.1c.6-.1 1.1-.1 1.7-.1c1.9-.1 3.8.5 5.3 1.6V25h2v-8z"
                                                      class="clr-i-outline clr-i-outline-path-2"/>
                                                <path fill="#54BA4A"
                                                      d="M23.4 7.8c.5-1.2 1.9-1.8 3.2-1.3c1.2.5 1.8 1.9 1.3 3.2c-.4.9-1.3 1.5-2.2 1.5c-.2 0-.5 0-.7-.1c.1.5.1 1 .1 1.4v.6c.2 0 .4.1.6.1c2.5 0 4.5-2 4.5-4.4c0-2.5-2-4.5-4.4-4.5c-1.6 0-3 .8-3.8 2.2c.5.3 1 .7 1.4 1.3"
                                                      class="clr-i-outline clr-i-outline-path-3"/>
                                                <path fill="#54BA4A"
                                                      d="M12 16.4q-.6-.9-.9-2.1h-.8c-2.6-.1-5.1.8-7 2.4L3 17v8h2v-7.2c1.6-1.1 3.4-1.7 5.3-1.6c.6 0 1.2.1 1.7.2"
                                                      class="clr-i-outline clr-i-outline-path-4"/>
                                                <path fill="#54BA4A"
                                                      d="M10.3 13.1c.2 0 .4 0 .6-.1v-.6c0-.5 0-1 .1-1.4c-.2.1-.5.1-.7.1c-1.3 0-2.4-1.1-2.4-2.4S9 6.3 10.3 6.3c1 0 1.9.6 2.3 1.5c.4-.5 1-1 1.5-1.4c-1.3-2.1-4-2.8-6.1-1.5s-2.8 4-1.5 6.1c.8 1.3 2.2 2.1 3.8 2.1"
                                                      class="clr-i-outline clr-i-outline-path-5"/>
                                                <path fill="#54BA4A"
                                                      d="m26.1 22.7l-.2-.3c-2-2.2-4.8-3.5-7.8-3.4c-3-.1-5.9 1.2-7.9 3.4l-.2.3v7.6c0 .9.7 1.7 1.7 1.7h12.8c.9 0 1.7-.8 1.7-1.7v-7.6zm-2 7.3H12v-6.6c1.6-1.6 3.8-2.4 6.1-2.4c2.2-.1 4.4.8 6 2.4z"
                                                      class="clr-i-outline clr-i-outline-path-6"/>
                                                <path fill="none" d="M0 0h36v36H0z"/>
                                            </svg>
                                            <svg class="half-circle svg-fill">
                                                <use href="{{ asset('assets/svg/icon-sprite.svg#halfcircle') }}"></use>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4><span class="counter"
                                                  data-target="{{ $entities->get('users_count') }}">{{priceFormat($entities->get('users_count')) ?? 0 }}</span>
                                        </h4><span
                                            class="f-light">کل کاربران</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Geofence and Vehicles count -->
            <div class="col-xxl-auto col-xl-3 col-sm-6 box-col-6">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card widget-1">
                            <div class="card-body">
                                <div class="widget-content">
                                    <div class="widget-round dark">
                                        <div class="bg-round">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48"
                                                 viewBox="0 0 24 24">
                                                <path fill="#3D3D3D"
                                                      d="M5.374 6.026A2.75 2.75 0 0 1 8.028 4h4.83a2.75 2.75 0 0 1 2.368 1.352l2.508 4.246l2.183.546A2.75 2.75 0 0 1 22 12.812V14.5a2.75 2.75 0 0 1-1.585 2.492a3.251 3.251 0 0 1-6.258.258H9.343a3.251 3.251 0 0 1-6.32-.61A2.75 2.75 0 0 1 2 14.5v-2.25a2.75 2.75 0 0 1 2.422-2.73zm8.664 9.724a3.25 3.25 0 0 1 6.274-.591c.12-.191.188-.417.188-.659v-1.688a1.25 1.25 0 0 0-.947-1.213L17.156 11H4.75c-.69 0-1.25.56-1.25 1.25v2.267a3.25 3.25 0 0 1 5.962 1.233zM11 9.5h4.934l-2-3.386a1.25 1.25 0 0 0-1.076-.614H11zm-1.5-4H8.028a1.25 1.25 0 0 0-1.206.921L5.982 9.5H9.5zm-3.25 9a1.75 1.75 0 1 0 0 3.5a1.75 1.75 0 0 0 0-3.5m9.25 1.75a1.75 1.75 0 1 0 3.5 0a1.75 1.75 0 0 0-3.5 0"/>
                                            </svg>
                                            <svg class="half-circle svg-fill">
                                                <use href="{{ asset('assets/svg/icon-sprite.svg#halfcircle') }}"></use>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4><span class="counter"
                                                  data-target="{{ $entities->get('vehicles_count') }}">{{ priceFormat($entities->get('vehicles_count')) ?? 0 }}</span>
                                        </h4><span
                                            class="f-light">کل وسایل نقلیه</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="card widget-1">
                            <div class="card-body">
                                <div class="widget-content">
                                    <div class="widget-round warning">
                                        <div class="bg-round">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48"
                                                 viewBox="0 0 48 48">
                                                <path fill="none" stroke="#FFB829" stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      d="M24 3a4.77 4.77 0 1 0 4.77 4.77A4.78 4.78 0 0 0 24 3M7.69 14.86a4.77 4.77 0 1 0 4.76 4.77a4.76 4.76 0 0 0-4.76-4.77m32.64 0a4.77 4.77 0 1 0 4.77 4.77a4.77 4.77 0 0 0-4.77-4.77M13.92 34.05a4.77 4.77 0 1 0 4.77 4.77a4.76 4.76 0 0 0-4.77-4.77m20.18 0a4.77 4.77 0 1 0 4.76 4.77a4.76 4.76 0 0 0-4.76-4.77"/>
                                                <path fill="none" stroke="#FFB829" stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      d="M19.27 8.35a17.24 17.24 0 0 0-9.73 6.87m28.51.22a17.28 17.28 0 0 0-9.34-6.94m8.7 26.89A17.1 17.1 0 0 0 40.93 25v-.61M18 41.22a17.2 17.2 0 0 0 5.68 1a17 17 0 0 0 6.2-1.22M6.53 24.25v.72a17.16 17.16 0 0 0 3.77 10.76"/>
                                            </svg>
                                            <svg class="half-circle svg-fill">
                                                <use href="{{ asset('assets/svg/icon-sprite.svg#halfcircle') }}"></use>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4><span class="counter"
                                                  data-target="{{ $entities->get('geofences_count') }}">{{ priceFormat($entities->get('geofences_count')) ?? 0 }}</span>
                                        </h4><span
                                            class="f-light">کل حصارها</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- inactive Users and Devices count -->
            <div class="col-xxl-auto col-xl-3 col-sm-5 order-xl-0 order-sm-1">
                <div class="card">
                    <div class="card-header card-no-border pb-0">
                        <div class="header-top student-header">
                            <h5>دستگاه ها و کاربران غیرفعال</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="std-class-chart">
                            @if($entities->get('in_active_users_count') && $entities->get('in_active_devices_count'))
                                <div x-data="deviceBarChart($el)"></div>
                            @else
                                <div class="alert alert-primary rounded-3">
                                    <p class="mb-0 text-center">فاقد دستگاه یا کابر غیرفعال</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- Top 5 Users Who most device have -->
            <div class="col-xxl-4 col-md-6 appointment-sec box-col-3">
                <div class="appointment">
                    <div class="card">
                        <div class="card-header card-no-border">
                            <div class="header-top">
                                <h5 class="m-0">بیشترین تعداد دستگاه</h5>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="table-responsive">
                                <table class="table table-bordernone">
                                    <thead>
                                    <tr>
                                        <th>نام کاربر</th>
                                        <th>سرگروه</th>
                                        <th class="text-end">تعداد دستگاه</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($entities->get('topDeviceUsers') as $user)
                                        <tr>
                                            <td>
                                                <a class="d-block f-w-500"
                                                   href="{{ route('user.show', $user->id) }}">{{ $user->name }}</a>
                                            </td>
                                            <td>{{ $user->joinedCompaniesList }}</td>
                                            <td class="text-end">
                                                <p class="m-0 font-success text-center">{{ $user->devices_count }}</p>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3">کاربری یافت نشد..</td>
                                        </tr>
                                    @endforelse

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Average Total distance By Date -->
            <div class="col-xxl-8 col-lg-6 box-col-6 ord-xl-2 ord-md-3 box-ord-2">
                <div class="card" x-data="distanceChart($refs.chartEl)">
                    <div class="card-header card-no-border">
                        <div class="header-top">
                            <h5>میانگین کل مسافت طی شده در <strong x-text="dateName"></strong> گذشته</h5>
                            <div class="card-header-right-icon">
                                <div class="dropdown custom-dropdown">
                                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                            aria-expanded="false">هفت روز
                                    </button>
                                    <ul class="dropdown-menu text-start">
                                        <li><a class="dropdown-item" href="javascript:void(0)"
                                               @click="filterDistance(0)">هفت روز</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)"
                                               @click="filterDistance(1)">14 روز</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)"
                                               @click="filterDistance(2)">یک ماه</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div x-ref="chartEl"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/chart/apex-chart/apex-chart.js') }}"></script>
    <script src="{{ asset('assets/js/chart/apex-chart/stock-prices.js') }}"></script>
    <script src="{{ asset('assets/js/height-equal.js') }}"></script>
    <script src="{{ asset('assets/js/animation/wow/wow.min.js') }}"></script>
    <script src="{{ asset('assets/js/clock.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard/default.js') }}"></script>
    <script>new WOW().init();</script>



    <script>
        window.addEventListener('alpine:init', () => {

            // in-active Device , Users count
            //----------------------------------------
            Alpine.data('deviceBarChart', (el) => ({
                options: {
                    series: [{{ $entities->get('in_active_users_count') ?? 0 }}, {{ $entities->get('in_active_devices_count') ?? 0 }}],
                    labels: ["دستگاه ها", "کاربران"],
                    chart: {
                        height: 185,
                        type: "donut",
                    },
                    plotOptions: {
                        pie: {
                            expandOnClick: false,
                            donut: {
                                size: "75%",
                                labels: {
                                    show: true,
                                    name: {
                                        offsetY: 4,
                                    },
                                    value: {
                                        fontSize: "12px",
                                        offsetY: 10,
                                        fontFamily: "inherit",
                                        fontWeight: 400,
                                        color: "#52526C",
                                    },
                                    total: {
                                        show: true,
                                        fontSize: "15px",
                                        fontWeight: 500,
                                        fontFamily: "inherit",
                                        color: "#313641",
                                        label: "مجموع",
                                        formatter: (w) => w.globals.seriesTotals.reduce((a, b) => a + b, 0),
                                    },
                                },
                            },
                        },
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    colors: ["#479AF3", "#ffb829"],
                    fill: {
                        type: "solid",
                    },
                    legend: {
                        show: true,
                        position: "bottom",
                        horizontalAlign: "center",
                        fontSize: "12px",
                        fontFamily: "inherit",
                        fontWeight: 500,
                        labels: {
                            colors: "#585B62",
                        },
                        formatter: function (seriesName, opts) {
                            return [seriesName, " - ", opts.w.globals.series[opts.seriesIndex]];
                        },
                        markers: {
                            width: 8,
                            height: 8,
                        },
                    },
                    stroke: {
                        width: 0,
                    },
                    responsive: [
                        {
                            breakpoint: 576,
                            options: {
                                chart: {
                                    height: 280,
                                },
                            },
                        },
                    ],
                },
                init() {
                    new ApexCharts(el, this.options).render();
                }

            }))

            // Avg trips distance in a period chart
            //----------------------------------------
            Alpine.data('distanceChart', (el) => ({
                chart: null,
                options: {
                    chart: {
                        height: 350,
                        type: "area",
                        toolbar: {
                            show: false,
                        },
                        zoom: {
                            enabled: false,
                        },
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    stroke: {
                        curve: "smooth",
                    },
                    series: [
                        {
                            name: "مسافت‌طی‌شده",
                            data: [],
                        },
                    ],

                    xaxis: {
                        type: "date",
                        categories: [],
                    },
                    tooltip: {
                        x: {
                            format: "dd/MM",
                        },
                        custom: function({ series, seriesIndex, dataPointIndex, w }) {
                            const distance = series[seriesIndex][dataPointIndex];
                            return `<div class="apexcharts-tooltip-title" style="direction: rtl; text-align: right;">
            میانگین مسافت طی شده: ${distance.toFixed()} کیلومتر
        </div>`;
                        }
                    },
                    colors: [CubaAdminConfig.primary],
                },
                dateName: 'هفت روز',

                init() {
                    this.chart = new ApexCharts(el, this.options);
                    this.chart.render();


                    this.getTotalDistance(30);
                },

                async getTotalDistance($num = 7) {
                    const response = await fetch(`/get-total-distance/${$num}`);
                    const data = await response.json();

                    let categories = [];
                    let seriesData = [];

                    Object.entries(data.dailyAvgDistance).forEach(([date, distance]) => {
                        categories.push(date);
                        seriesData.push(distance);
                    });


                    this.chart.updateOptions({
                        xaxis: {
                            categories: categories
                        },
                        series: [{
                            data: seriesData
                        }]
                    });


                },

                filterDistance($num) {
                    switch ($num) {
                        case 0:
                            this.dateName = 'هفت روز';
                            this.getTotalDistance(7);
                            break;
                        case 1:
                            this.dateName = 'چهارده روز';
                            this.getTotalDistance(14);
                            break;
                        case 2:
                            this.dateName = 'یک ماه';
                            this.getTotalDistance(30);
                            break;
                    }
                }

            }))
        })

    </script>
@endpush
