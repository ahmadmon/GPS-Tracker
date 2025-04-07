<?php

namespace App\Livewire\Wallet;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

class WalletPage extends Component
{
    #[Validate('required|numeric|min:10000|max:50000000')]
    public string $amount = "10,000";
    #[Validate('nullable|string|min:5')]
    public string $description;


    #[Title('کیف پول من')]
    public function render()
    {
        $user = Auth::user();

        return view('livewire.wallet.wallet-page',[
            'user' => $user,
            'wallet' => $user->wallet
        ]);
    }


    public function handleWallet(): void
    {
        $this->amount = str_replace(',', '', $this->amount);

        $this->validate();

        dd($this->amount);
    }
}
