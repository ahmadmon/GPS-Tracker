@if(session('error-alert'))
    <div class="alert alert-light-danger light alert-dismissible fade show text-dark border-right-wrapper" role="alert" x-init="$el.scrollIntoView({behavior: 'smooth', block: 'nearest'})">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             class="feather feather-alert-octagon text-danger">
            <polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <p class="mb-0 text-danger-emphasis text-wrap d-inline">{!! nl2br(session('error-alert')) !!}</p>
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
