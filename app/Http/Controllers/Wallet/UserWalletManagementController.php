<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;

class UserWalletManagementController extends Controller
{
    public function index()
    {
        return view('wallet.user.index');
    }
}
