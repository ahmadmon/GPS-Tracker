<?php

namespace App\Http\Controllers\Subscription;

use App\Enums\Subscription\CancellationStatus;
use App\Http\Controllers\Controller;
use App\Jobs\SendSms;
use App\Models\SubscriptionCancellation;
use App\Models\User;
use App\Notifications\GenericNotification;
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


    public function approveRequest(string $id)
    {
        $cancellation = SubscriptionCancellation::with('subscription.wallet.walletable')->findOrFail($id);
        $user = $this->getUser($cancellation);

        $cancellation->update([
            'status' => CancellationStatus::REFUNDED,
            'refunded_at' => now()
        ]);

    }

    /**
     * @param Request $request
     * @param string $id
     */
    public function rejectRequest(Request $request, string $id)
    {
        $cancellation = SubscriptionCancellation::with('subscription.wallet.walletable')->findOrFail($id);
        $user = $this->getUser($cancellation);

        $cancellation->update([
            'status' => CancellationStatus::REJECTED,
            'rejection_reason' => $request->input('rejection_reason')
        ]);

        $user->notify(new GenericNotification($this->rejectedMessage(isSms: false), 'subscription-cancellation'));
        SendSms::dispatch($user->phone, $this->rejectedMessage($user->name));


        return back()->with('success-alert', 'درخواست با موفقیت رد شد.');
    }


    /**
     * @param string|null $name
     * @param bool $isSms
     * @return string
     */
    protected function rejectedMessage(?string $name = null, bool $isSms = true): string
    {
        if ($isSms) {
            return sprintf(
                "سلام %s عزیز،\n" .
                "متأسفانه درخواست لغو اشتراک شما رد شد. لطفاً برای مشاهده جزئیات و دلیل این تصمیم به بخش «تاریخچه اشتراک» در پنل کاربری مراجعه فرمایید.\n" .
                "در صورت نیاز به راهنمایی بیشتر، پشتیبانان ما آماده پاسخگویی هستند.\n" .
                "سامانه سمفا - رهیابی GPS",
                $name
            );
        }

        return "متأسفانه درخواست لغو اشتراک شما رد شد. لطفاً برای مشاهده جزئیات و دلیل این تصمیم به بخش «تاریخچه اشتراک» در پنل کاربری مراجعه فرمایید.";
    }


    /**
     * @param SubscriptionCancellation $cancellation
     * @return User
     */
    protected function getUser(SubscriptionCancellation $cancellation): User
    {
        $walletable = $cancellation->subscription->wallet->walletable;
        return $walletable instanceof User ?
            $walletable :
            $walletable->manager;
    }
}
