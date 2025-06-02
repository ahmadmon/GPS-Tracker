@if(session('no-subscriber-alert'))
    <div class="alert-box bg-danger-subtle p-2">
        <div class="alert alert-dismissible justify-content-center p-0 fade show" role="alert">
            <div class="alert-body">
                <img class="mb-2" src="{{ asset('assets/images/custom/access-denied.png') }}" width="100" height="100" alt="access-denied">
                <h6 class="mb-1">برای دسترسی به سامانه باید اشتراک فعال داشته باشید.</h6>
                    <p class="text-wrap d-inline">برای خرید اشتراک ابتدا موجودی کیف پول خود را افزایش دهید سپس طرح اشتراک مناسب خود را انتخاب
                        و خریداری کنید.</p>
                <div class="button-box">
                    <a href="{{ route('profile.subscription.index') }}" class="btn btn-primary-gradien">خرید اشتراک</a>
                </div>
            </div>
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif
