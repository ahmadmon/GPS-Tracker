<div class="modal fade" id="status-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModal" aria-hidden="true"
     wire:ignore.self>
    <div class="modal-dialog" role="document">
        @if($deviceStatus)
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title">
                        <h5>آخــرین وضعیت دستگاه {{ str($deviceStatus->device->name)->replace('دستگاه', '') }}</h5>
                        <small class="fw-bold d-block">شماره سریال: {{ $deviceStatus->device->serial }}</small>
                        <small class="fw-bold mt-2">آخرین بروزرسانی: <span class="text-muted">{{ jalaliDate($deviceStatus->updated_at ?: $deviceStatus->created_at, format: '%d %B %Y H:i:s') }}</span></small>
                    </div>
                    <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="modal-toggle-wrapper">
{{--                        <div class="large-modal-header justify-content-center"><i class="icofont icofont-ui-settings me-2"></i>--}}
{{--                            <h6>وضعیت دستــگاه و خــودرو</h6>--}}
{{--                        </div>--}}
{{--                        <hr>--}}
                        <div>
                            <div class="navigation-option">
                                <ul>
                                    <li><a class="p-0" href="javascript:void(0)"><img class="me-2" src="{{ asset('assets/images/icons/air-conditioning.png') }}" alt="air-conditioning" />وضعیت تهویه خودرو: <span @class(['badge dana', $deviceStatus->ac_status ? 'bg-success' : 'bg-danger'])>{{ ($deviceStatus->ac_status ? 'روشن' : 'خاموش') ?? '-' }}</span></a></li>
                                    <li><a class="p-0" href="javascript:void(0)"><img class="me-2" src="{{ asset('assets/images/icons/power-button.png') }}" alt="ignition" />وضعیت موتور: <span @class(['badge dana', $deviceStatus->ignition ? 'bg-success' : 'bg-danger'])>{{ ($deviceStatus->ignition ? 'روشن' : 'خاموش') ?? '-' }}</span></a></li>
                                    <li><a class="p-0" href="javascript:void(0)"><img class="me-2" src="{{ asset('assets/images/icons/lighting.png') }}" alt="charging" />وضعیت شارژ: <span @class(['badge dana', $deviceStatus->charging ? 'bg-success' : 'bg-danger'])>{{ ($deviceStatus->charging ? 'درحال شارژ' : 'در حال شارژ نیست') ?? '-' }}</span></a></li>
                                    <li><a class="p-0" href="javascript:void(0)"><img class="me-2" src="{{ asset('assets/images/icons/gps-tracking.png') }}" alt="Gps Tracking" />وضعیت GPS: <span @class(['badge dana', $deviceStatus->gps_tracking ? 'bg-success' : 'bg-danger'])>{{ ($deviceStatus->gps_tracking ? 'در حال ارسال موقعیت' : 'قطع شده') ?? '-' }}</span></a></li>
                                    <li><a class="p-0" href="javascript:void(0)"><img class="me-2" src="{{ asset('assets/images/icons/relay.png') }}" alt="Relay" />وضعیت رله: <span @class(['badge dana', $deviceStatus->relay_state ? 'bg-success' : 'bg-danger'])>{{ ($deviceStatus->relay_state ? 'وصل' : 'قطع') ?? '-' }}</span></a></li>
                                    <hr>
                                    <li><a class="p-0" href="javascript:void(0)"><img class="me-2" src="{{ asset('assets/images/icons/bell.png') }}" alt="Alarm" />نوع هشدار: <span @class(['badge dana', 'bg-' . $deviceStatus?->alarm_type?->badge()['color']])>{{ $deviceStatus->alarm_type->badge()['name'] ?? '-' }}</span></a></li>
                                    <li><a class="p-0" href="javascript:void(0)"><img class="me-2" src="{{ asset('assets/images/icons/voltageLevel.png') }}" alt="Voltage Level" />میزان باتری: <span class="{{ $deviceStatus->batteryStatus['iconClass'] }}">{{ $deviceStatus->batteryStatus['text'] }} ( <i class="fa fa-solid fa-{{ $deviceStatus->batteryStatus['iconClass'] }}"></i> )</span></a></li>
                                    <li><a class="p-0" href="javascript:void(0)"><img class="me-2" src="{{ asset('assets/images/icons/signal-level.png') }}" alt="Signal Level" />کیفیت سیگنال: <span class="{{ $deviceStatus->signalStatus['color'] }}">{{ $deviceStatus->signalStatus['text'] }}</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
