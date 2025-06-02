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

    <section wire:ignore>
        <x-partials.alert.warning-alert/>
    </section>
    <div class="row">
        <div class="row">
            <div class="col-md-4">
                <div class="email-right-aside bookmark-tabcontent">
                    <div class="card email-body radius-left">
                        <div class="ps-0">
                            <div class="tab-content">
                                <div id="pills-created" role="tabpanel">
                                    <div class="card mb-0">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5>دستگاه ها</h5>
                                            <div wire:ignore>
                                                <ul class="tg-list common-flex">
                                                    <li class="tg-list-item">
                                                        <input class="tgl tgl-skewed" id="cb3" type="checkbox"
                                                               wire:click="changeMode" @checked($onlineMode)>
                                                        <label class="tgl-btn" data-tg-off="آفلاین" data-tg-on="آنلاین"
                                                               for="cb3"></label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-body p-0 device-sidebar overflow-y-auto">
                                            <div class="col-12 position-absolute" style="z-index: 7">
                                                <input class="form-control rounded-0" id="search" type="text"
                                                       placeholder="جستجو بر اساس نام یا شناسه..." aria-label="جسنجو"
                                                       wire:model.live.debounce.850ms="search">
                                                <x-input-error :messages="$errors->get('search')" class="mt-1"/>
                                            </div>
                                            <div class="taskadd visible-scroll">
                                                <div class="table-responsive text-nowrap" x-data="{
                                                    scrollToMap() {
                                                        const mapEl = document.getElementById('map');
                                                        if (mapEl) {
                                                            mapEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                                        }
                                                    }
                                                }">
                                                    <table class="table mt-5"
                                                           @if ($onlineMode) wire:poll.4s.keep-alive @endif>
                                                        <tbody>
                                                        <tr>
                                                            <td @class([
                                                                    'w-100 d-none justify-content-start align-items-center',
                                                                    'd-flex' => $errors->has('selected'),
                                                                ])>
                                                                <x-input-error :messages="$errors->get('selected')"
                                                                               class="mt-1"/>
                                                            </td>
                                                            <td @class([
                                                                    'w-100 d-none justify-content-start align-items-center',
                                                                    'd-flex' => $errors->has('selected.*'),
                                                                ])>
                                                                <x-input-error :messages="$errors->get('selected.*')"
                                                                               class="mt-1"/>
                                                            </td>
                                                        </tr>
                                                        @forelse($devices as $key => $device)
                                                            <tr wire:key="{{ $device->id }}"
                                                                @click="scrollToMap()" class="device-section">
                                                                <td
                                                                    class="w-100 d-flex justify-content-between align-items-center">
                                                                    <div
                                                                        class="d-flex justify-content-start align-items-center me-3">
                                                                        <input type="checkbox"
                                                                               id="input-{{ $key }}"
                                                                               value="{{ $device->id }}"
                                                                               class="ui-checkbox me-2"
                                                                               @checked(in_array($device->id, $selected))
                                                                               wire:model.live="selected">
                                                                        <label for="input-{{ $key }}"
                                                                               class="cursor-pointer">
                                                                            <h6 class="task_title_0 device-title">
                                                                                دستگاه
                                                                                {{ str($device->name)->replace('دستگاه', '') }}
                                                                                @if($device->lastStatus())
                                                                                    <i class="fa fa-info-circle cursor-pointer"
                                                                                       data-bs-toggle="modal"
                                                                                       data-bs-target="#status-modal"
                                                                                       @click="$event.preventDefault(); $wire.handleDeviceStatus({{ $device->id }})"></i>
                                                                                @endif
                                                                            </h6>
                                                                            <small
                                                                                class="project_name_0 text-muted d-block">{{ $device->serial }}</small>
                                                                            <small
                                                                                class="project_name_0 text-muted d-block">{{ $device->user?->name }}</small>
                                                                        </label>
                                                                    </div>
                                                                    @if ($device->lastLocation())
                                                                        <div
                                                                            class="d-flex justify-content-end align-items-center">
                                                                                <span>KM/H <strong
                                                                                        class="text-success">{{ json_decode($device?->lastLocation()->device_stats)?->speed }}</strong></span>
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr x-intersect.once="$wire.loadMore()"></tr>
                                                        @empty
                                                            <tr>
                                                                <p class="text-muted text-center">دستگاهی یافت نشد.
                                                                </p>
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
                        <div class="row">
                            <div class="col-12 col-xl-6" x-data="dateTimeRange($refs.dateRangeInp)">
                                <label class="form-label" for="datetime-range">انتخاب تاریخ: </label>
                                <div class=" d-flex align-items-center justify-content-start">
                                    <div class="input-group flatpicker-calender">
                                        <div class="input-group flatpicker-calender" wire:ignore>
                                            <input class="form-control" id="datetime-range" type="date"
                                                   wire:model="dateTimeRange" x-ref="dateRangeInp">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-start align-items-center ms-2"><a
                                            class="btn btn-primary f-w-500" type="button" wire:click="handleTrip"
                                            :class="disabled && 'disabled'">فیلتر</a>
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('dateTimeRange')" class="mt-1"/>
                            </div>

                            <div class="col-12 col-xl-6" wire:ignore x-data="trackplayer">
                                <label class="form-label">تنظیمات پخش:</label>
                                <div class="d-flex flex-row-reverse justify-content-between align-items-center">
                                    <div class="d-flex">
                                        <button class="btn btn-warning-gradien px-2" data-bs-toggle="tooltip"
                                                @click="removeTracker()" data-bs-placement="top" title="حذف پخش کننده">
                                            <img class="img-fluid"
                                                 src="{{ asset('assets/libs/leaflet/track-player/icons/close-square-svgrepo-com.svg') }}"
                                                 width="24" height="24" alt="">
                                        </button>

                                        <button class="btn btn-warning-gradien px-2 mx-1" data-bs-toggle="tooltip"
                                                @click="changeSpeed()" :disabled="!btnStatus" data-bs-placement="top"
                                                title="سرعت">
                                            <span class="text-dark f-w-900" x-show="displaySpeed"
                                                  x-text="displaySpeed"></span>
                                            <img class="img-fluid" x-show="!displaySpeed"
                                                 src="{{ asset('assets/libs/leaflet/track-player/icons/playback-speed-svgrepo-com.svg') }}"
                                                 width="24" height="24" alt="سرعت">
                                        </button>

                                        <button class="btn btn-warning-gradien  px-2" @click="togglePlay()"
                                                :disabled="!btnStatus" :title="isPlaying ? 'توقف کردن' : 'پخش کردن'">
                                            <img class="img-fluid"
                                                 :src="isPlaying ?
                                                    '{{ asset('assets/libs/leaflet/track-player/icons/pause-circle-svgrepo-com.svg') }}' :
                                                    '{{ asset('assets/libs/leaflet/track-player/icons/play-circle-svgrepo-com.svg') }}'"
                                                 width="24" height="24" alt="">
                                        </button>
                                    </div>

                                    <div class="range-d-slider">
                                        <div x-ref="slider_thumb" class="range-d-slider_thumb"></div>
                                        <div class="range-d-slider_line">
                                            <div x-ref="slider_line" class="range-d-slider_line-fill"></div>
                                        </div>
                                        <input x-ref="slider_input" class="range-d-slider_input" type="range"
                                               :disabled="!btnStatus" @input="handleSliderInput($event)" min="0"
                                               max="100" :value="currentProgress">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card-body z-1 position-relative" x-data="mapComponent" wire:ignore>
                    <div @remove-all.window="removeWayPoints()"></div>
                    <div @appear-waypoints.window="showWaypoints(trips)"></div>
                    <div class="map-js-height" x-ref="map" id="map"></div>

                    <div id="spinner-loader" wire:loading
                         wire:target="updatedSelected, updateDeviceLocation, handleTrip, loadMore, changeMode">
                        <div class="bg-loader">
                            <div class="loader"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <x-partials.modals.device-status-modal :$deviceStatus/>


</div>

@assets

@include('custom.script.map-page-assets')

@endassets

@script

@include('custom.script.map-page-alpine-scripts')

@endscript
