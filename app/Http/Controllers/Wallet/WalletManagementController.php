<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Models\Wallet;

class WalletManagementController extends Controller
{
    public function show(Wallet $wallet)
    {
        $transactions = $wallet->transactions()->latest()->cursor();

        return view('wallet.user.show', [
            'wallet' => $wallet,
            'transactions' => $transactions,
            'user' => $wallet->walletable
        ]);
    }

}
