<?php

namespace App\Http\Controllers\Wallet;

use App\Enums\Subscription\Plan\PlanType;
use App\Enums\Subscription\SubscriptionStatus;
use App\Facades\Subscription;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscribeRequest;
use App\Jobs\SendSms;
use App\Models\Subscription as SubscriptionModel;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
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

    public function history(?Wallet $wallet = null)
    {
        $wallet = is_null($wallet) ? Auth::user()->wallet : $wallet;

        $isUser = $wallet->walletable instanceof User;

        $subscriptions = $wallet->subscription()
            ->with(['wallet', 'plan'])
            ->whereIn('status', [SubscriptionStatus::EXPIRED, SubscriptionStatus::CANCELED])
            ->latest()
            ->get();

        $canceledSubscription = $subscriptions->where('status', 'canceled')->first();
        $isPending = $canceledSubscription?->cancellation()->exists() && $canceledSubscription?->cancellation?->status->isPending();
        if($isPending) session()->put('info-alert', "درخواست بازگشت وجه شما در حال بررسی است.\nپس از تایید توسط پشتیبانی، مبلغ به شماره شبا اعلام شده واریز خواهد شد.");

        return view('profile.subscription.history', compact('subscriptions', 'isUser', 'wallet'));

    }


    public function subscribe(SubscribeRequest $request, Wallet $wallet)
    {
        $inputs = (object)$request->validated();
        $plan = SubscriptionPlan::findOrFail((int)$inputs->plan);
        $price = $plan->price;
        $walletable = $wallet->walletable;

        if ($wallet->balance >= $price) {
            DB::transaction(function () use ($wallet, $price, $plan, $inputs, $walletable) {

                $wallet->decrement('balance', $price);

                $this->createTransaction([
                    'amount' => $price,
                    'description' => "برداشت برای خرید اشتراک {$plan->name}"
                ], $wallet);
                $isUser = $walletable instanceof User;

                $subscription = Subscription::subscribe($wallet, $plan, $inputs->auto_renew);
                if (!$isUser) {
                    Subscription::subscribeSubsets($wallet, $plan); // activation subscribes for manger and subsets

                    foreach ($walletable->users as $subset) {
                        SendSms::dispatch($subset->phone, $this->smsSubsetsMessage($subscription->end_at, $walletable->name));
                    }
                }

                // Sending a success message via SMS
                $phoneNumber = $isUser ? $walletable->phone : $walletable->manager->phone;
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
            ->where('status', SubscriptionStatus::ACTIVE)
            ->first();
        if (!$subscription) return to_route('profile.wallet')->with('error-alert', "درحال حاضر اشتراک فعالی ندارید.\nبرای خرید اشتراک ابتدا موجودی کیف پول خود را افزایش دهید سپس طرح اشتراک مناسب خود را انتخاب و خریداری کنید.");


        $isUser = $subscription->wallet->walletable instanceof User;


        return view('profile.subscription.show', compact('subscription', 'isUser'));
    }

    public function renew(string $id)
    {
        $subscription = SubscriptionModel::with('wallet.walletable', 'plan:price,id,name,duration')
            ->findOrFail($id);

        $wallet = $subscription->wallet;
        $walletable = $wallet->walletable;
        $isUser = $walletable instanceof User;
        $user = $isUser ? $walletable : $walletable->manager;
        $plan = $subscription->plan;


        if ($wallet->balance >= $plan->price) {

            DB::transaction(function () use ($subscription, $isUser, $walletable, $wallet, $plan) {
                Subscription::renew($subscription);
                if (!$isUser) {
                    Subscription::renewSubsets($walletable);
                }

                $wallet->decrement('balance', $plan->price);

                $this->createTransaction([
                    'amount' => $plan->price,
                    'description' => "برداشت برای تمدید اشتراک {$plan->name}"
                ], $wallet);
            });


            $message = $this->smsSubscriptionSuccessMessage($plan, $subscription->end_at, $isUser, $walletable->name, isRenew: true);
            if (!$isUser) {
                foreach ($walletable->users as $subset) {
                    SendSms::dispatch($subset->phone, $this->smsSubsetsMessage($subscription->end_at, $walletable->name, isRenew: true));
                }
            }
            SendSms::dispatch($user->phone, $message);

            return to_route('profile.wallet')->with('success-alert', "✅ تمدید اشتراک با موفقیت انجام شد!\n شما اکنون دسترسی کامل به بخش های سامانه را دارید.\n\n برای مشاهده جزئیات بیشتر اشتراک, به جزئیات اشتراک مراجعه کنید.");
        } else {
            $walletPageUrl = route('profile.wallet');
            return back()->with('error-alert', "❌ تمدید اشتراک ناموفق بود!<br> 💳به نظر می‌رسد موجودی کیف پول شما کافی نیست. برای افزایش موجودی کیف پول، لطفاً به لینک زیر مراجعه کنید: <br><a href='{$walletPageUrl}' >افزایش موجودی</a>");
        }

    }

    public function toggleAutoActivation(SubscriptionModel $subscription)
    {
        $subscription->auto_renew = $subscription->auto_renew == 0 ? 1 : 0;
        $subscription->save();

        return response()->json(['status' => true, 'data' => (bool)$subscription->auto_renew]);
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


    private function smsSubscriptionSuccessMessage($plan, $expirationDate, $isUser, $companyName, $isRenew = false): string
    {
        $type = $isRenew ? 'تمدید' : 'فعال‌سازی';
        if (!$isUser && $companyName) {
            return sprintf(
                "سمفا - سامانه هوشمند ردیابی GPS\n\n" .
                "🎉 اشتراک '%s' برای سازمان '%s' با موفقیت %s شد.\n" .
                "📅 تاریخ انقضا: %s\n\n" .
                "برای مشاهده اشتراک، به جزئیات اشتراک مراجعه کنید.",
                $plan->name,
                $companyName,
                $type,
                jalaliDate($expirationDate)
            );
        }

        return sprintf(
            "سمفا - سامانه هوشمند ردیابی GPS\n\n" .
            "🎉 اشتراک '%s' برای شما با موفقیت %s شد.\n" .
            "📅 تاریخ انقضا: %s\n\n" .
            "برای مشاهده اشتراک، به جزئیات اشتراک مراجعه کنید.",
            $plan->name,
            $type,
            jalaliDate($expirationDate)
        );
    }

    private function smsSubsetsMessage($expirationDate, $companyName, $isRenew = false)
    {
        $type = $isRenew ? 'تمدید' : 'فعال‌سازی';
        return sprintf(
            "سمفا - سامانه هوشمند ردیابی GPS\n\n" .
            "با توجه به %s اشتراک سازمان «%s»، اشتراک شما نیز به‌صورت خودکار %s شد.\n" .
            "📅 تاریخ انقضای جدید: %s\n" .
            "شما همچنان به تمامی امکانات سامانه دسترسی دارید. برای مشاهده جزئیات بیشتر، به بخش جزئیات اشتراک‌ مراجعه فرمایید.",
            $type,
            $companyName,
            $type,
            $expirationDate
        );
    }

}
