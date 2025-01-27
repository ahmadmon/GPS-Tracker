<div x-data="{ selected: '{{ old('command','') }}' }">
    <div class="mb-3">
        <label class="form-label" for="command">دستور مربوط به دستگاه را انتخاب کنید.
            <sup class="text-danger">*</sup>
        </label>
        <select class="form-control" id="command" name="command" x-model="selected">
            <option value="" selected>انتخاب کنید</option>
            <option value="0" @selected(old('command') == 0)>فعالسازی دستگاه</option>
            <option value="1" @selected(old('command') == 1)>تنظیم Apn خط</option>
            <option value="2" @selected(old('command') == 2)>زمانبندی ارسال موقعیت</option>
            <option value="3" @selected(old('command') == 3)>تنظیم رمز عبور</option>
            <option value="4" @selected(old('command') == 4)>معرفی شماره ادمین</option>
            <option value="5" @selected(old('command') == 5)>بازگردانی دستگاه به حالت کارخانه</option>
            <option value="6" @selected(old('command') == 6)>سایر دستورات</option>
        </select>
        <x-input-error :messages="$errors->get('command')" class="mt-2"/>
    </div>

    <section class="row">
        <!-- APN -->
        <div class="mb-3" x-cloak x-show="parseInt(selected) === 1">
            <label class="form-label" for="selected-1">اپراتور سیم کارت داخل دستگاه را انتخاب کنید.
                <sup class="text-danger">*</sup>
            </label>
            <select class="form-select" name="apn" id="selected-1">
                <option value="mtnirancel" @selected(old('apn') == 'mtnirancel')>ایرانسل</option>
                <option value="mcinet" @selected(old('apn') == 'mcinet')>همراه اول</option>
                <option value="RighTel" @selected(old('apn') == 'RighTel')>رایتل</option>
                <option value="aptel" @selected(old('apn') == 'aptel')>آپتل</option>
            </select>
            <x-input-error :messages="$errors->get('apn')" class="mt-2"/>
        </div>
        <!-- Upload Time Interval -->
        <div class="mb-3" x-cloak x-show="parseInt(selected) === 2">
            <label class="form-label" for="selected-2">زمان را بر حسب ثانیه وارد کنید
                <sup class="text-danger">*</sup>
            </label>
            <small class="text-muted d-block">در این بخش شما میتوانید مشخص کنید دستگاه در حالت حرکت
                چند ثانیه یکبارموقعیت را روی سامانه نشان دهد.</small>
            <strong class="text-muted d-block">حداقل زمان مجاز: 10</strong>
            <input class="form-control" id="selected-2" name="interval" type="number"
                   value="{{ old('interval') }}"
                   placeholder="برای مثال: 120">
            <x-input-error :messages="$errors->get('interval')" class="mt-2"/>
        </div>
        <!-- Change Device Password -->
        <div class="mb-3" x-cloak x-show="parseInt(selected) === 3">
            <label class="form-label" for="selected-3">رمز عبور را وارد کنید
                <sup class="text-danger">*</sup>
            </label>
            <small class="text-muted d-block">رمز عبور باید شامل 4 کاراکتر باشد</small>
            <small class="text-muted d-block">رمز عبور بپیشفرض برابر است با: <strong
                    class="text-danger">0000</strong></small>
            <input class="form-control" id="selected-3" name="password" type="number"
                   value="{{ old('password') }}"
                   placeholder="برای مثال: 0000">
            <x-input-error :messages="$errors->get('password')" class="mt-2"/>
        </div>
        <!-- Set Admin Number -->
        <div class="mb-3" x-cloak x-show="parseInt(selected) === 4">
            <label class="form-label" for="selected-4">شماره تماس ادمین
                <sup class="text-danger">*</sup>
            </label>
            <small class="text-muted d-block">در این بخش، شماره ادمین را وارد کنید تا در صورت نیاز،
                امکان دریافت اطلاعات از دستگاه فراهم شود.</small>
            <input class="form-control" id="selected-4" name="phones[0]" type="number"
                   value="{{ old('phones.0') }}"
                   placeholder="برای مثال: 09123456789">
            <x-input-error :messages="$errors->get('phones.0')" class="mt-2"/>
        </div>
        <!-- Others Command -->
        <div class="mb-3" x-cloak x-show="parseInt(selected) === 6">
            <label class="form-label" for="selected-8">دستور مورد نظر را وارد کنید
                <sup class="text-danger">*</sup>
            </label>
            {{--            <small class="text-muted d-block">پاسخ ارسال‌شده از سوی دستگاه به شماره‌ای که به‌عنوان شماره اضطراری (SOS) ثبت شده است، ارسال خواهد شد.</small>--}}
            <div>
                <input class="form-control" id="selected-8" name="other" type="text"
                       style="text-transform: uppercase"
                       value="{{ old('other') }}"
                       dir="ltr"
                       placeholder="COMMAND">
            </div>
            <x-input-error :messages="$errors->get('other')" class="mt-2"/>
        </div>
    </section>
</div>
