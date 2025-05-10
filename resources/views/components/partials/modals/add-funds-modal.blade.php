<div class="modal fade modal-bookmark" id="addFundsModal" tabindex="-1" role="dialog"
     wire:ignore.self
     aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex justify-content-center align-items-center">
                    <i data-feather="credit-card" class="me-2"></i>
                    <h5 class="modal-title" id="exampleModalLabel">
                        <span>افزایش موجودی کیف پــول</span>
                    </h5>
                </div>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-bookmark needs-validation" id="bookmark-form" novalidate wire:submit="handleWallet">
                    <div class="row">
                        <div class="mb-3 mt-0 col-md-12" x-data="{
                            init(){
                                new Cleave($refs.walletAmount,{
                                    numeral: true
                                })
                            }
                        }">
                            <label for="wallet-amount">مبلغ مورد نظر جهت شارژ کیف پول (تومان)
                                <sup class="text-danger fw-bold">*</sup>
                            </label>
                            <input class="form-control" id="wallet-amount" x-ref="walletAmount" type="text" required
                                   placeholder="مبلغ را وارد کنید..."
                                   wire:model="amount"
                                   autocomplete="off">
                            <x-input-error :messages="$errors->get('amount')" class="mt-2"/>
                        </div>
                        @if($isManager)
                            <div class="row" x-data="{ type: $wire.chargeTarget ?? 0 }">
                                <div class="mb-3 col-md-6 my-0">
                                    <label for="charge-target">افزایش موجودی برای</label>
                                    <select class="form-control form-select" wire:model="chargeTarget"
                                            x-model="type"
                                            id="charge-target">
                                        <option value="0">کیف پول شخصی من</option>
                                        <option value="1">کیف پول سازمان</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('chargeTarget')" class="mt-2"/>

                                </div>
                                @if($user->companies->count() !== 1)
                                    <div class="mb-3 col-md-6 my-0">
                                        <label for="company">سازمان</label>
                                        <select class="form-control form-select" :disabled="parseInt(type) === 0"
                                                wire:model="companyID"
                                                id="company">
                                            <option value="">انتخاب کنید</option>
                                            @foreach($user->companies as $company)
                                                <option value="{{ $company->id }}">{{ $company?->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('companyID')" class="mt-2"/>
                                    </div>
                                @else
                                    <div class="mb-3 col-md-6 my-0">
                                        <label for="company">سازمان</label>
                                        <input class="form-control" :disabled="parseInt(type) === 0" type="text"
                                               readonly
                                               value="{{ $user->companies()->first()->name }}" id="company">
                                    </div>
                                @endif
                            </div>
                        @endif
                        <div class="mb-3 col-md-12 my-0">
                            <label for="wallet-description">توضیحات</label>
                            <textarea class="form-control" autocomplete="off"
                                      id="wallet-description"
                                      wire:model="description"
                                      placeholder="مثلاً: واریز برای استفاده از خرید اشتراک ماهانه..."></textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2"/>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-secondary" id="Bookmark" type="submit">
                            <div class="d-flex justify-content-center align-items-center" wire:loading.class="d-none"
                                 wire:target="handleWallet">
                                <span class="">پرداخت آنلاین</span>
                                <div class="d-inline" wire:ignore>
                                    <i data-feather="external-link" class="ms-1"></i>
                                </div>
                            </div>

                            <x-partials.loaders.livewire.spinner target="handleWallet"/>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
