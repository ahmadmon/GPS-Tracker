<?php

namespace App\Http\Controllers\Subscription;

use App\Facades\Acl;
use App\Facades\Subscription as SubscriptionService;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\GrantSubscriptionRequest;
use App\Models\Company;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionManagementController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        [$model, $isUser] = $this->getWalletTypeAndUser($request);

        if ($isUser) {
            Acl::authorize('user-subscriptions-list');
        } else {
            Acl::authorize('company-subscriptions-list');
        }

        $subscriptions = Subscription::with(['plan', 'wallet.walletable'])
            ->whereHas('wallet', fn($q) => $q->where('walletable_type', $model))
            ->latest()
            ->get();


        return view('subscription-management.index', compact('subscriptions', 'isUser'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        [$model, $isUser] = $this->getWalletTypeAndUser($request);

        if ($isUser) {
            Acl::authorize('create-user-subscription');
        } else {
            Acl::authorize('create-company-subscription');
        }

        $plans = SubscriptionPlan::where('status', 1)->whereIn('type', ['both', $isUser ? 'personal' : 'company'])->get(['id', 'name']);

        $noneSubscribedModels = $model::whereDoesntHave('wallet.subscription', static fn($q) => $q->where('status', 'active'))
            ->where('status', 1)
            ->when($isUser, fn($q) => $q->whereIn('user_type', [0, 3]))
            ->get(['id as value', 'name'])
            ->toArray();


        return view('subscription-management.create', compact(['isUser', 'plans', 'noneSubscribedModels']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GrantSubscriptionRequest $request)
    {
        $inputs = (object)$request->validated();

        $model = $inputs->type === 'user' ? User::class : Company::class;
        $plan = SubscriptionPlan::find($inputs->plan, ['id', 'duration', 'name', 'price']);
        $isUser = $inputs->type === 'user';

        if ($isUser) {
            Acl::authorize('create-user-subscription');
        } else {
            Acl::authorize('create-company-subscription');
        }


        $failedEntities = [];

        DB::transaction(static function () use ($inputs, $model, $plan, $isUser, &$failedEntities) {
            foreach ($inputs->entity_ids as $id) {
                $entity = $model::find($id);
                $wallet = $entity->wallet;

                if ($inputs->withdraw_wallet) {
                    if ($wallet->balance < $plan->price) {
                        $failedEntities[] = $entity->name ?? "شناسه {$id} موجودی ناکافی.";
                        continue;
                    }

                    $wallet->decrement('balance', $plan->price);
                }

                if (!$wallet->hasSubscription()) {
                    SubscriptionService::subscribe($wallet, $plan, $inputs->auto_renew);
                    if (!$isUser) {
                        SubscriptionService::subscribeSubsets($wallet, $plan);
                    }
                }

            }
        });

        [$alertType, $msg] = $this->handleMessage($failedEntities, count($inputs->entity_ids), $isUser, $plan->name);
        return to_route('subscription-management.index', ['type' => $inputs->type])
            ->with("{$alertType}-alert", $msg);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort(404);
    }


    /*
    |--------------------------------------------------------------------------
    | Private Helper Functions
    |--------------------------------------------------------------------------
    |
    |
    */

    /**
     * @param Request $request
     * @return array
     */
    private function getWalletTypeAndUser(Request $request): array
    {
        if (!$request->has('type')) {
            abort(404);
        }

        $type = $request->input('type');
        $model = $type === 'user' ? User::class : Company::class;
        $isUser = $type === 'user';

        return [$model, $isUser];
    }


    private function handleMessage(array $failedEntities, int $totalEntities, bool $isUser, $planName)
    {
        $failedCount = count($failedEntities);

        $typeMsg = $isUser ? 'کاربران' : 'سازمان‌های';
        $failedTypeMsg = $isUser ? 'کاربر' : 'سازمان';
        $msg = "اشتراک {$planName} به {$typeMsg} انتخاب‌شده اختصاص داده شد.";

        if ($failedCount === $totalEntities) {
            $alertType = 'error';
            $msg = "هیچ {$failedTypeMsg}ی موفق به دریافت اشتراک {$planName} نشد.\n لطفا موجودی کیف پول‌ها را بررسی کنید.";
        } elseif ($failedCount > 0) {
            $alertType = 'warning';
            $failedList = implode('، ', $failedEntities);
            $msg .= " البته به دلیل مشکلات زیر برخی موفق نشدند: {$failedList}";
        } else {
            $alertType = 'success';
        }

        return [$alertType, $msg];

    }
}
