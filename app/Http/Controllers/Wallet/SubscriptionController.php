<?php

namespace App\Http\Controllers\Wallet;

use App\Enums\Subscription\Plan\PlanType;
use App\Facades\Subscription;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscribeRequest;
use App\Jobs\SendSms;
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
            DB::transaction(function () use ($wallet, $price, $plan, $inputs) {

                $wallet->decrement('balance', $price);

                $this->createTransaction([
                    'amount' => $price,
                    'description' => "برداشت برای خرید اشتراک {$plan->name}"
                ], $wallet);
                $isUser = $wallet->walletable instanceof User;

                $subscription = Subscription::subscribe($wallet, $plan, $inputs->auto_renew);
                if (!$isUser) {
                    Subscription::subscribeSubsets($wallet, $plan); // activation subscribes for manger and subsets
                }

                // Sending a success message via SMS
                $phoneNumber = $isUser ? $wallet->walletable->phone : $wallet->walletable->manager->phone;
                $message = $this->smsSubscriptionSuccessMessage($plan, $subscription->end_at, $isUser, $wallet->walletable->name);
                SendSms::dispatch($phoneNumber, $message);
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


    private function smsSubscriptionSuccessMessage($plan, $expirationDate, $isUser, $companyName): string
    {
        if (!$isUser && $companyName) {
            return sprintf(
                "سمفا - سامانه هوشمند ردیابی GPS\n\n" .
                "🎉 اشتراک '%s' برای سازمان '%s' با موفقیت فعال شد.\n" .
                "📅 تاریخ انقضا: %s\n\n" .
                "برای مشاهده اشتراک، به جزئیات اشتراک مراجعه کنید.",
                $plan->name,
                $companyName,
                jalaliDate($expirationDate)
            );
        }

        return sprintf(
            "سمفا - سامانه هوشمند ردیابی GPS\n\n" .
            "🎉 اشتراک '%s' برای شما با موفقیت فعال شد.\n" .
            "📅 تاریخ انقضا: %s\n\n" .
            "برای مشاهده اشتراک، به جزئیات اشتراک مراجعه کنید.",
            $plan->name,
            jalaliDate($expirationDate)
        );
    }

}
