@if(session('info-alert'))
    <div class="alert alert-light-info light alert-dismissible fade show text-dark border-right-wrapper"
         role="alert" x-init="$el.scrollIntoView({behavior: 'smooth', block: 'nearest'})">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 16 16">
            <path fill="black"
                  d="M8 9a.75.75 0 0 1-.75-.75v-3.5a.75.75 0 0 1 1.5 0v3.5A.75.75 0 0 1 8 9m-1 3a1 1 0 1 1 2 0a1 1 0 0 1-2 0"/>
            <path fill="black" fill-rule="evenodd"
                  d="m.325 11.6l5.02-9.99c1.1-2.19 4.21-2.19 5.31 0l5.02 9.99c1 2-.436 4.36-2.66 4.36h-10c-2.22 0-3.66-2.36-2.66-4.36zm.894.449l5.02-9.99c.733-1.46 2.79-1.46 3.52 0l5.02 9.99c.676 1.35-.301 2.91-1.76 2.91h-10c-1.46 0-2.44-1.57-1.76-2.91z"
                  clip-rule="evenodd"/>
        </svg>
        <p class="mb-0 text-success-emphasis text-wrap d-inline">{!! nl2br(session('info-alert')) !!}</p>
        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
