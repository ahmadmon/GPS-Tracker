@if(session('no-subscriber-alert'))
    <div class="alert-box bg-danger-subtle p-2">
        <div class="alert alert-dismissible justify-content-center p-0 fade show" role="alert">
            <div class="alert-body">
                <svg>
                    <use href="../assets/svg/icon-sprite.svg#alert-popup"></use>
                </svg>
                <h6 class="mb-1">برای دسترسی به سامانه باید اشتراک فعال داشته باشید.</h6>
                <p>برای خرید اشتراک ابتدا موجودی کیف پول خود را افزایش دهید سپس طرح اشتراک مناسب خود را انتخاب
                    و خریداری کنید.</p>
                <div class="button-box">
                    <button class="btn btn-primary-gradien">خرید اشتراک</button>
                </div>
            </div>
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif
