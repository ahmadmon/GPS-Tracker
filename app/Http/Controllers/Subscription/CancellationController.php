<?php

namespace App\Http\Controllers\Subscription;

use App\Enums\Subscription\CancellationStatus;
use App\Enums\Subscription\SubscriptionStatus;
use App\Facades\Acl;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Jobs\SendSms;
use App\Models\SubscriptionCancellation;
use App\Models\User;
use App\Notifications\GenericNotification;
use Illuminate\Http\Request;

class CancellationController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Acl::authorize('revoke-user-subscription');

        $cancellationRequests = SubscriptionCancellation::with(['subscription.plan:id,name', 'subscription.wallet.walletable:id,name'])
            ->where('status', CancellationStatus::PENDING)
            ->latest()
            ->cursor();

        return view('subscription-cancellation.index', compact('cancellationRequests'));
    }


    /**
     * @param string $id
     */
    public function approveRequest(string $id)
    {
        Acl::authorize('revoke-user-subscription');


        $cancellation = SubscriptionCancellation::with('subscription.wallet.walletable')->findOrFail($id);
        $user = $this->getUser($cancellation);

        $cancellation->update([
            'status' => CancellationStatus::REFUNDED,
            'refunded_at' => now()
        ]);

        $cancellation->subscription()->update([
            'status' => SubscriptionStatus::CANCELED,
            'auto_renew' => false,
            'canceled_at' => now()
        ]);

        $user->notify(new GenericNotification($this->approvedMessage(isSms: false), 'subscription-cancellation'));
        SendSms::dispatch($user->phone, $this->approvedMessage(name: $user->name));

        return back()->with('success-alert', 'درخواست با موفقیت تایید شد.');
    }

    /**
     * @param Request $request
     * @param string $id
     */
    public function rejectRequest(Request $request, string $id)
    {
        Acl::authorize('revoke-user-subscription');


        $cancellation = SubscriptionCancellation::with('subscription.wallet.walletable')->findOrFail($id);
        $user = $this->getUser($cancellation);

        $cancellation->update([
            'status' => CancellationStatus::REJECTED,
            'rejection_reason' => $request->input('rejection_reason')
        ]);

        $user->notify(new GenericNotification($this->rejectedMessage(isSms: false), 'subscription-cancellation'));
        SendSms::dispatch($user->phone, $this->rejectedMessage(name: $user->name));


        return back()->with('success-alert', 'درخواست با موفقیت رد شد.');
    }

    /*
    |--------------------------------------------------------------------------
    | Private Helper Functions
    |--------------------------------------------------------------------------
    |
    |
    */


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
     * @param string|null $name
     * @param bool $isSms
     * @return string
     */
    protected function approvedMessage(?string $name = null, bool $isSms = true): string
    {
        if ($isSms) {
            return sprintf(
                "سلام %s عزیز،\n" .
                "درخواست لغو اشتراک شما با موفقیت تایید شد و مبلغ قابل عودت به شماره شبا اعلام‌شده واریز گردید.\n" .
                "از همراهی شما سپاسگزاریم. در صورت نیاز به اطلاعات بیشتر، با پشتیبانی در تماس باشید.\n" .
                "سامانه سمفا - رهیابی GPS",
                $name
            );
        }

        return "درخواست لغو اشتراک شما تایید شد و مبلغ به حساب بانکی شما واریز گردید.\nبرای مشاهده جزئیات بیشتر، به بخش تاریخچه اشتراک‌ها مراجعه فرمایید.";
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
