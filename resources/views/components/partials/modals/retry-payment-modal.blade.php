<div x-data="retryPaymentModal">
    <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggle"
         tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-toggle-wrapper">
                        <h6>آیا مایل هستید این تراکنش را از طریق درگاه پرداخت تکمیل کنید یا دستی وضعیت آن را مشخص
                            نمایید؟</h6>
                        <form :action="gatewayUrl ?? '#'" method="post" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 mt-4 mb-1">انتقال به درگاه پرداخت</button>
                        </form>
                        <button class="btn w-100 pb-0 dark-toggle-btn" type="button"
                                data-bs-target="#exampleModalToggle2"
                                data-bs-toggle="modal">تغییر وضعیت دستی
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModalToggle2" aria-hidden="true" aria-labelledby="exampleModalToggle2"
         tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>تغییر وضعیت تراکنش <small class="text-muted" x-text="transaction?.transaction_number"></small>
                    </h4>
                    <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="modal-toggle-wrapper">
                        <form :action="url ?? '#'" method="post" x-ref="changeStatusForm">
                            @csrf
                            <label for="trx-status">لطفا انتخاب کنید</label>
                            <select name="trx-status" id="trx-status" class="form-select">
                                <option value="{{ \App\Enums\Wallet\TransactionStatus::SUCCESS->value }}">تایید تراکنش
                                </option>
                                <option value="{{ \App\Enums\Wallet\TransactionStatus::FAILED->value }}">لـــغو تراکنش
                                </option>
                            </select>
                            <div>
                                <p class="text-danger mt-2 fw-bold text-center mb-0" x-text="error ?? ''"></p>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-dark" type="button" data-bs-target="#exampleModalToggle"
                            data-bs-toggle="modal">بازگشت به مرحله قبل
                    </button>
                    <button type="button" class="btn btn-primary" @click="$refs.changeStatusForm.submit()">ثبت</button>
                </div>
            </div>
        </div>
    </div>

</div>
