<?php

namespace App\Livewire\Company;

use App\Models\Company;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class AddSubsets extends Component
{
    use WithPagination;

    public Company $company;

    public string $search = '';
    public int $perPage = 10;
    public array $selectedUsers = [];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }


    public function mount(): void
    {
        $this->selectedUsers = $this->company->users()->pluck('id')->toArray();
    }

    public function render()
    {
        $users = User::where([['status', 1], ['user_type', 0]])
            ->withCount(['devices', 'vehicles'])
            ->whereNotIn('id', $this->selectedUsers)
            ->when($this->search !== '', function ($q) {
                return $q->whereLike('name', "%{$this->search}%")
                    ->orWhereLike('phone', "%{$this->search}%");
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);


        return view('livewire.company.add-subsets', [
            'users' => $users,
        ]);
    }

    public function handleSelection(int $userId): void
    {
        if (!User::find($userId)->exists()) {
            return;
        }
        $this->company->users()->syncWithoutDetaching([$userId]);

        $this->selectedUsers[] = $userId;

        $this->dispatch('userAddedToSubsets')->to(RemoveSubsets::class);
    }

    #[On('userDetachedFromSubsets')]
    public function refreshList(int $userId): void
    {
        $index = array_search($userId, $this->selectedUsers);
        if ($index !== false) unset($this->selectedUsers[$index]);

        $this->resetPage();
    }
}
