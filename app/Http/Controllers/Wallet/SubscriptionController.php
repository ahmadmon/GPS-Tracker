<?php

namespace App\Http\Controllers\Wallet;

use App\Enums\Subscription\Plan\PlanType;
use App\Facades\Subscription;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscribeRequest;
use App\Models\Subscription as SubscriptionModel;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function index(?Wallet $wallet = null)
    {
        $wallet = is_null($wallet) ? Auth::user()->wallet : $wallet;
        if ($wallet->hasSubscription()) return to_route('profile.subscription.show'); // Checking if the wallet owner is a subscriber?

        $isUser = $wallet->walletable instanceof User;

        $specificType = array_values(array_filter(PlanType::values(), fn($value) => $value !== ($isUser ? PlanType::COMPANY->value : PlanType::PERSONAL->value)));
        $plans = SubscriptionPlan::where('status', 1)
            ->whereIn('type', $specificType)
            ->latest()
            ->get();

        return view('profile.subscription.index', [
            'plans' => $plans,
            'wallet' => $wallet,
            'isUser' => $isUser
        ]);
    }


    public function subscribe(SubscribeRequest $request, Wallet $wallet)
    {
        $inputs = (object)$request->validated();
        $plan = SubscriptionPlan::findOrFail((int)$inputs->plan);
        $price = $plan->price;

        if ($wallet->balance >= $price) {
            DB::transaction(function () use ($wallet, $price, $plan) {

                $wallet->decrement('balance', $price);

                $this->createTransaction([
                    'amount' => $price,
                    'description' => "برداشت برای خرید اشتراک {$plan->name}"
                ], $wallet);


                if ($wallet->walletable instanceof User) {
                    Subscription::subscribe($wallet, $plan);// activation subscribes for User
                } else {
                    Subscription::subscribe($wallet, $plan); // activation subscribes for Company
                    Subscription::subscribeSubsets($wallet, $plan); // activation subscribes for manger and subsets
                }
            });

            return to_route('profile.wallet')->with('success-alert', "✅ خرید اشتراک با موفقیت انجام شد!\n شما اکنون دسترسی کامل به بخش های سامانه را دارید.\n\n برای مشاهده جزئیات بیشتر اشتراک, به جزئیات اشتراک مراجعه کنید.");
        } else {
            $walletPageUrl = route('profile.wallet');
            return back()->with('error-alert', "❌ خرید اشتراک ناموفق بود!<br> 💳به نظر می‌رسد موجودی کیف پول شما کافی نیست. برای افزایش موجودی کیف پول، لطفاً به لینک زیر مراجعه کنید: <br><a href='{$walletPageUrl}' >افزایش موجودی</a>");
        }

    }


    public function show(?string $id = null)
    {
        $subscription = SubscriptionModel::with(['plan', 'wallet.walletable:id,name'])
            ->where('wallet_id', is_null($id) ? Auth::user()->wallet->id : $id)
            ->firstOrFail();

        $isUser = $subscription->wallet->walletable instanceof User;


        return view('profile.subscription.show', compact('subscription', 'isUser'));
    }

    public function toggleAutoActivation(SubscriptionModel $subscription)
    {
        $subscription->is_activated_automatically = $subscription->is_activated_automatically == 0 ? 1 : 0;
        $subscription->save();

        return response()->json(['status' => true, 'data' => (bool)$subscription->is_activated_automatically]);
    }

    /*
    |--------------------------------------------------------------------------
    | Private Helper Functions
    |--------------------------------------------------------------------------
    |
    |
    */

    private function createTransaction(array $info, Wallet $wallet)
    {
        $wallet->transactions()->create([
            'source_id' => $wallet->walletable_id,
            'source_type' => $wallet->walletable_type,
            'type' => 'debit',
            'status' => 'success',
            'amount' => $info['amount'],
            'description' => $info['description'] ?? null,
        ]);
    }
}
