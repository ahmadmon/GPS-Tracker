<li class="onhover-dropdown" wire:poll.60s>
    <div class="notification-box">
        <svg>
            <use href="{{ asset('assets/svg/icon-sprite.svg#notification') }}"></use>
        </svg>
        <span class="badge rounded-pill badge-secondary">{{ $notificationsCount }} </span>
    </div>
    <div class="onhover-show-div notification-dropdown">
        <h6 class="f-18 mb-0 dropdown-title">اعلان‌ها </h6>
        <ul>
            @foreach($notifications as $notification)
                <li class="b-l-{{ randomColor() }} border-4 shadow-10-dark" wire:click="markAsRead({{ $notification->id }})">
                    <a href="{{ $notification->data['type'] === 'subscription_expiry' ? route('profile.subscription.show') : 'javascript:void(0)' }}" class="text-decoration-none text-muted">
                        <small>{!! nl2br($notification->data['message']) !!} <span
                                class="font-danger">{{ jalaliDate($notification->created_at,ago: true) }} </span></small>
                    </a>
                </li>
            @endforeach

        </ul>
    </div>
</li>
