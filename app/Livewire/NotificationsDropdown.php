<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;

class NotificationsDropdown extends Component
{
    public int $notificationsCount = 0;
    public $notifications;
    public $user;

//    #[On('notificationUpdated')]
    public function booted(): void
    {
        $this->user = auth()->user();
        $this->notifications = $this->user->unreadNotifications;
        $this->notificationsCount = $this->notifications->count();
    }

    public function render()
    {
        return view('livewire.notifications-dropdown');
    }


    /**
     * @param string $notifId
     * @return Redirector|null
     */
    public function markAsRead(string $notifId): Redirector|null
    {
        $notification = $this->user->notifications()->find($notifId);
        $notification->markAsRead();

        if ($this->notificationsCount < 0)
            $this->notificationsCount -= 1;

        $this->dispatch('notificationUpdated');

        $route = $this->getRedirectRoute($notification->data['type']);
        return $route ? redirect($route) : null;
    }


    private function getRedirectRoute(string $name): ?string
    {
        return match ($name) {
            'subscription_expiry',
            'subscription_renewed',
            'subscription-expired', => route('profile.subscription.show'),

            'subscription_renewed_failed' => route('profile.wallet'),
            default => null
        };
    }
}
