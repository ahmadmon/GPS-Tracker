<?php

namespace App\Http\Controllers\Wallet;

use App\Enums\Subscription\Plan\PlanType;
use App\Facades\Subscription;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscribeRequest;
use App\Http\Services\Subscription\SubscriptionService;
use App\Models\Company;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function index(?Wallet $wallet = null)
    {
        $wallet = is_null($wallet) ? Auth::user()->wallet : $wallet;
        $isUser = $wallet->walletable instanceof User;

        $specificType = array_values(array_filter(PlanType::values(), fn($value) => $value !== ($isUser ? PlanType::COMPANY->value : PlanType::PERSONAL->value)));

        $plans = SubscriptionPlan::where('status', 1)
            ->whereIn('type', $specificType)
            ->latest()
            ->get();

        return view('profile.subscription', [
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
            $trxInfo = [
                'amount' => $price,
                'description' => "برداشت برای خرید اشتراک {$plan->name}"
            ];
            DB::transaction(function () use ($wallet, $price, $plan, $trxInfo) {

                $wallet->decrement('balance', $price);
                $this->createTransaction($trxInfo, $wallet);

                Subscription::subscribe($wallet, $plan);
            });

            return to_route('profile.wallet')->with('success-alert', "✅ خرید اشتراک با موفقیت انجام شد!\n شما اکنون دسترسی کامل به بخش های سامانه را دارید.\n\n برای مشاهده جزئیات بیشتر اشتراک, به جزئیات اشتراک مراجعه کنید.");
        } else {
            $walletPageUrl = route('profile.wallet');
            return back()->with('error-alert', "❌ خرید اشتراک ناموفق بود!<br> 💳به نظر می‌رسد موجودی کیف پول شما کافی نیست. برای افزایش موجودی کیف پول، لطفاً به لینک زیر مراجعه کنید: <br><a href='{$walletPageUrl}' >افزایش موجودی</a>");
        }

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
