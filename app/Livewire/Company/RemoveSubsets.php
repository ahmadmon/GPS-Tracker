<?php

namespace App\Livewire\Company;

use App\Models\Company;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class RemoveSubsets extends Component
{

    use WithPagination;

    public Company $company;

    public string $search = '';
    public int $perPage = 10;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function mount(): void
    {
        $this->updatedSearch();
        $this->company = $this->company->load('users');
    }

    public function render()
    {
        $selectedUsers = $this->company->users()
            ->withCount(['vehicles', 'devices'])
            ->whereLike('name', "%{$this->search}%")
            ->orWhereLike('phone', "%{$this->search}%")
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.company.remove-subsets', [
            'selectedUsers' => $selectedUsers
        ]);
    }

    public function handleSelection(int $userId): void
    {
        if (!User::find($userId)->exists()) {
            return;
        }

        $this->company->users()->detach($userId);

        $this->dispatch('userDetachedFromSubsets', userId: $userId)->to(AddSubsets::class);

    }

    #[On('userAddedToSubsets')]
    public function refreshList(): void
    {
        $this->resetPage();
    }
}
