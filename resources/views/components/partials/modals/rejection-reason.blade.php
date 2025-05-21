<div x-data="rejectionReason">
    <div class="modal fade" id="rejection-reason-modal" aria-hidden="true" aria-labelledby="rejection-reason-modal"
         tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form :action="`/subscription-cancellation/reject-request/${id}`" method="post" x-ref="rejectionReasonForm"
                      @submit.prevent="submitForm">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h4>رد کردن درخواست</h4>
                        <button class="btn-close py-0" type="button" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-toggle-wrapper" @cancellation-request-id.window="id = $event.detail.id">
                            <div @@error="required = $valid($event.detail, 'required')">
                                <label for="trx-status" class="form-label">لطفا دلیل لغو درخواست را بنویسید
                                    <sup class="txt-danger fw-bold">*</sup>
                                </label>
                                <textarea class="form-control" name="rejection_reason" id="rejection-reason" rows="5"
                                          x-model="reason" x-validation.required="reason" @input="required = false"></textarea>
                                <div class="text-center">
                                    <small class="text-danger mt-2 fw-bold mb-0" x-show="required">
                                        فیلد الزامی است.
                                    </small>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-dark" type="button" data-bs-dismiss="modal" aria-label="Close">انصراف
                        </button>
                        <button type="submit" class="btn btn-primary" >ثبت
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
