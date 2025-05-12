<form wire:submit="store" autocomplete="off">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label" for="name">نام و نام خانوادگی</label>
            <input class="form-control" id="name" name="name" wire:model="name"
                   type="text">
            <x-input-error :messages="$errors->get('name')" class="mt-2"/>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label" for="phone">شماره تماس</label>
            <input class="form-control" id="phone" dir="ltr" name="phone"
                   wire:model="phone"
                   type="text">
            <x-input-error :messages="$errors->get('phone')" class="mt-2"/>

            @if($show)
                <div class="form-group mt-3">
                    <label class="col-form-label" for="otp_code">کد ارسال شده به شماره <span
                            class="text-muted">{{ $phone }}</span> را وارد کنید.
                    </label>
                    <div class="row">
                        <div class="col-12" x-data="{ otp: '' }" x-init="console.log()">
                            <input class="form-control text-center mb-1"
                                   autofocus autocomplete="otp_code"
                                   x-model="otp" @keyup="if(otp.length === 4) $wire.verify()"
                                   id="otp_code" wire:model="otp_code"
                                   value="{{ old('otp_code') }}"
                                   type="number" placeholder="00 00">
                            <x-input-error :messages="$errors->get('otp_code')" class="mt-2"/>
                        </div>
                        <div class="text-end mt-2" x-data="{
                                        time: parseInt($wire.duration),
                                        minutes: Math.floor(time / 60),
                                        seconds: time % 60,
                                        otp: '',

                                        startTimer() {
                                            const interval = setInterval(() => {
                                                if (this.time > 0) {
                                                    this.time--;
                                                    this.minutes = Math.floor(this.time / 60);
                                                    this.seconds = this.time % 60;
                                                    this.seconds = this.seconds < 10 ? '0' + this.seconds : this.seconds;
                                                } else {
                                                    clearInterval(interval);
                                                    $wire.reset('otp_code');
                                                    $wire.showAlert('error', 'زمان وارد کردن کد OTP به پایان رسیده است.');
                                                }
                                            }, 1000);
                                        }
                                    }" x-init="startTimer()">
                            <span class="fw-bold" x-show="time > 0" x-text="minutes + ':' + seconds"></span>
{{--                            <span class="fw-bold text-danger" x-show="time <= 0">زمان منقضی شده است</span>--}}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @if($companiesName)
        <div class="col-12 mb-3" )>
            @role(['manager'])
            <p>شما مدیر سازمان های زیر هستید.</p>
            @else
                <p>شما عضو سازمان های زیر هستید.</p
                @endrole
                <ul class="fw-bold list-group">
                    @foreach($companiesName as $name)
                        <li class="list-group-item border-left-{{ randomColor() }}">{{ $name }}</li>
                    @endforeach
                </ul>
        </div>
    @endif

    <div class="col-12 mt-2">
        <button class="btn btn-primary" type="submit">
            <span wire:loading.remove wire:target="store">ثـــبت</span>
            <x-partials.loaders.livewire.spinner target="store"/>
        </button>
    </div>
</form>
