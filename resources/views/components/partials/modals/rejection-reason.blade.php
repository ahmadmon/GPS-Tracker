<div x-data="rejectionReason">
    <div class="modal fade" id="rejection-reason-modal" aria-hidden="true" aria-labelledby="rejection-reason-modal"
         tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>رد کردن درخواست</h4>
                    <button class="btn-close py-0" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="modal-toggle-wrapper" @cancellation-request-id.window="id = $event.detail.id">
                        <form :action="`/reject-request/${id}`" method="post" x-ref="rejectionReasonForm">
                            @csrf
                            <label for="trx-status" class="form-label">لطفا دلیل لغو درخواست را بنویسید
                                <sup class="txt-danger fw-bold">*</sup>
                            </label>
                            <textarea class="form-control" name="rejection_reason" id="rejection-reason" x-model="reason"></textarea>
                            <div>
                                <p class="text-danger mt-2 fw-bold text-center mb-0" x-text="error ?? ''"></p>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-dark" type="button" data-bs-dismiss="modal" aria-label="Close">انصراف
                    </button>
                    <button type="button" class="btn btn-primary" @click="$refs.rejectionReasonForm.submit()">ثبت
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
