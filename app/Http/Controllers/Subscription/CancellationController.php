<?php

namespace App\Http\Controllers\Subscription;

use App\Enums\Subscription\CancellationStatus;
use App\Http\Controllers\Controller;
use App\Models\SubscriptionCancellation;
use Illuminate\Http\Request;

class CancellationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cancellationRequests = SubscriptionCancellation::with(['subscription.plan:id,name', 'subscription.wallet.walletable:id,name'])
            ->where('status', CancellationStatus::PENDING)
            ->latest()
            ->cursor();

        return view('subscription-cancellation.index', compact('cancellationRequests'));
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubscriptionCancellation $subscriptionCancellation)
    {
        dd($subscriptionCancellation);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
