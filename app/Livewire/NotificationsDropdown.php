<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class NotificationsDropdown extends Component
{
    public int $notificationsCount = 0;
    public $notifications;
    public $user;

    #[On('notificationUpdated')]
    public function mount()
    {
        $this->user = auth()->user();
        $this->notifications = $this->user->unreadNotifications;
        $this->notificationsCount = $this->notifications->count();
    }

    public function render()
    {
        return view('livewire.notifications-dropdown');
    }


    public function markAsRead(string $notifId)
    {
        $notification = $this->user->notifications()->find($notifId);
        dd($notification);
        $notification->markAsRead();

        $this->notificationsCount -= 1;

        $this->dispatch('notificationUpdated')->self();
    }
}
