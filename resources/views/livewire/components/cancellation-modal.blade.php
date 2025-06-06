<div class="modal fade modal-bookmark" id="subscriptionCancellation" tabindex="-1" role="dialog"
     wire:ignore.self
     aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex justify-content-center align-items-center" wire:ignore>
                    <i data-feather="slash" class="me-2"></i>
                    <h5 class="modal-title" id="exampleModalLabel">
                        <span>لغـــو اشتراک</span>
                    </h5>
                </div>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row">
                <div class="col-xl-6 mb-3 mb-md-0">
                    <div class="border rounded-5 p-25 shadow-sm">
                        <h5 class="fw-bold">⚠️ شرایط لغو اشتراک</h5>
                        <ul class="mt-2 ms-2" style="list-style: initial">
                            <li>اگر از زمان خرید اشتراک شما کمتر از ۲۴ ساعت گذشته باشد، با وارد کردن شماره شبا، تمام
                                مبلغ پرداختی به حساب شما برگشت داده خواهد شد.
                            </li>
                            <li class="my-2">در صورتی که از زمان خرید کمتر از شش ماه گذشته باشد، ۳۰٪ مبلغ پرداختی به
                                عنوان جریمه کسر شده و ۷۰٪ آن به حساب شما برگشت داده خواهد شد.
                            </li>
                            <li>در صورتی که از زمان خرید بیش از شش ماه گذشته باشد، متأسفانه امکان بازگشت وجه وجود
                                ندارد.
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-6">
                    <form wire:submit="handleCancellation"
                          class="form-bookmark needs-validation" id="cancel-form" novalidate>
                        <div class="row">
                            <div class="mb-3 mt-0 col-md-12">

                                <div x-data="{
                                    walletRefund: $wire.walletRefund,
                                    iban: $wire.iban ? $wire.iban : 'IR'
                                }" x-init="$wire">
                                    <div class="mb-3">
                                        <div class="d-flex mt-2">
                                            <div class="text-end icon-state">
                                                <label class="switch mb-0">
                                                    <input type="checkbox"
                                                           x-model="walletRefund"
                                                           wire:model="walletRefund"

                                                    ><span
                                                        class="switch-state bg-black"></span>
                                                </label>
                                            </div>
                                            <label class="col-form-label m-l-10">واریز به کیف‌پول</label>
                                        </div>
                                        <x-input-error :messages="$errors->get('walletRefund')" class="mt-2"/>
                                    </div>
                                    <template x-if="walletRefund === false">
                                        @if($userSavedIbans->count() > 1)
                                            <div class="mb-3"
                                                 x-data="{ newIban: false, selected: '', show: false,

                                                 resetOptions(){
                                                  this.show = false
                                                  if(this.show){
                                                    this.selected = @js((string)$userSavedIbans->first(), JSON_THROW_ON_ERROR);
                                                  }else{
                                                    this.selected = '';
                                                    $wire.iban = '';
                                                  }
                                                  } }"
                                                 x-init="$watch('selected', value => { show = value === 'new-iban'; console.log(value)})">

                                                <label for="selectIbans" class="form-label"
                                                       x-text="show ? 'شماره شبا جدید را وارد کنید' : 'شماره شبا را انتخاب کنید'">
                                                    <sup class="text-danger fw-bold">*</sup>
                                                </label>
                                                <template x-if="show === false">
                                                    <select wire:model="iban" id="selectIbans" class="form-select"
                                                            x-model="selected">
                                                        <option value="" dir="rtl">انتخاب کنید</option>
                                                        @foreach($userSavedIbans as $userIban)
                                                            <option
                                                                value="{{ $userIban }}">{{ formatIban($userIban) }}</option>
                                                        @endforeach
                                                        <option value="new-iban" dir="rtl">+ شماره شبا جدید</option>
                                                    </select>
                                                </template>
                                                <template x-if="show === true">
                                                    <div>
                                                        <input type="text" class="form-control" dir="ltr"
                                                               wire:model="iban"
                                                               x-model="iban"
                                                               id="iban"
                                                        >
                                                        <div>
                                                            <button type="button"
                                                                    class="btn p-0 txt-primary text-decoration-underline"
                                                                    @click="resetOptions">انتخاب شماره شبا
                                                            </button>
                                                        </div>
                                                    </div>
                                                </template>
                                                <x-input-error :messages="$errors->get('iban')" class="mt-2"/>
                                            </div>
                                        @else
                                            <div class="mb-3">
                                                <label for="iban">شماره شبا را وارد کنید
                                                    <sup class="text-danger fw-bold">*</sup>
                                                </label>
                                                <input type="text" class="form-control" dir="ltr"
                                                       wire:model="iban"
                                                       x-model="iban"
                                                       id="iban"
                                                >
                                                <x-input-error :messages="$errors->get('iban')" class="mt-2"/>
                                            </div>
                                        @endif
                                    </template>
                                </div>
                                <div class="mb-3">
                                    <label for="reason">دلیل لغو اشتراک خود را بنویسید
                                        <sup class="text-danger fw-bold">*</sup>
                                    </label>
                                    <textarea class="form-control" autocomplete="off" required
                                              wire:model="reason"
                                              id="reason"
                                    ></textarea>
                                    <x-input-error :messages="$errors->get('reason')" class="mt-2"/>
                                </div>


                            </div>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-dark me-2" type="button" data-bs-dismiss="modal">انصراف</button>
                                <button class="btn btn-danger" id="Bookmark"
                                        type="submit">
                                    <span wire:loading.remove wire:target="handleCancellation">لغو اشتراک</span>
                                    <x-partials.loaders.livewire.spinner target="handleCancellation"/>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
