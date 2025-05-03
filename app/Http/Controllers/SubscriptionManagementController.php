<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class SubscriptionManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!$request->has('type')) abort(404);
        $walletType = $request->input('type') === 'user' ? User::class : Company::class;
        $isUser = $request->input('type') === 'user';

        $subscriptions = Subscription::with(['plan', 'wallet.walletable'])
            ->whereHas('wallet', fn($q) => $q->where('walletable_type', $walletType))
            ->latest()
            ->get();


        return view('subscription-management.index', compact('subscriptions', 'isUser'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
