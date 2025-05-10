<?php

namespace App\Livewire\Wallet;

use App\Http\Services\Payment\PaymentService;
use App\Jobs\SendSms;
use App\Models\Company;
use App\Models\Payment;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Morilog\Jalali\Jalalian;

class WalletPage extends Component
{
    /**
     * Wallet charging variables
     * -------------------------------------------
     **/
    #[Validate('required|numeric|min:10000|max:50000000', as: 'مبلغ')]
    public string $amount = "10,000";
    #[Validate('nullable|string|min:5', as: 'توضیحات')]
    public ?string $description = null;
    #[Validate('required|numeric|in:0,1', as: 'نوع کیف پول')]
    public int $chargeTarget = 0; // 0 => user's wallet , 1 => company wallet
    #[Validate('nullable|required_if:chargeTarget,1|numeric|exists:companies,id', as: 'سازمان')]
    public ?int $companyID = null;

    public bool $isManager = false;

    protected function messages()
    {
        return [
            'companyID.required_if' => 'فیلد :attribute الزامی است.'
        ];
    }

    /**
     * Filtering variables
     * -------------------------------------------
     **/
    #[Url(as: 's', except: null)]
    #[Validate('nullable|string', as: 'جستجو')]
    public ?string $search = null;

    #[Url(as: 'type', except: null)]
    #[Validate('nullable|string|in:credit,debit,', as: 'نوع')]
    public ?string $type = null;

    #[Url(as: 'status', except: null)]
    #[Validate('nullable|string|in:success,pending,failed,', as: 'وضعیت')]
    public ?string $status = null;

    #[Url(as: 'date', except: null)]
    #[Validate('nullable|date', as: 'تاریخ')]
    public ?string $date = null;

    /**
     * Other variables
     * -------------------------------------------
     **/
    public int $personalTake = 10;
    public int $companyTake = 10;


    public function mount(): void
    {
        if (!auth()->user()->wallet) to_route('home');

        if (Auth::user()->subsets()->isNotEmpty()) {
            $this->isManager = true;
        }

    }

    #[Title('کیف پول من')]
    public function render()
    {
        $user = Auth::user();

        return view('livewire.wallet.wallet-page', [
            'user' => $user,
            'wallet' => $user->wallet,
            'myTransactions' => $this->myTransactions(),
            'companiesTransactions' => $this->isManager ? $this->companiesTransactions() : []
        ]);
    }


    /**
     * @param PaymentService $paymentService
     */
    public function handleWallet(PaymentService $paymentService)
    {
        $this->amount = $this->convertToInt($this->amount);
        $this->validate();

        // Creating transaction and payment record
        $transaction = $this->createTransaction();
        $payment = $this->createPayment($transaction);

        try {
            // Transfer to payment gateway
            $paymentGateway = $paymentService->paymentPage($transaction, $payment);
            return redirect()->away($paymentGateway);

        } catch (Exception $e) {
            Log::error('payment failed', [$e->getMessage()]);

            return to_route('profile.wallet')->with('error-alert', "خطا در اتصال به درگاه پرداخت.\n لطفاً چند دقیقه دیگر مجدداً تلاش نمایید.");
        }
    }

    /**
     * Repayment for pending transactions
     *
     * @param string $transactionId
     * @param PaymentService $paymentService
     */
    public function retryPayment(string $transactionId, PaymentService $paymentService)
    {
        $transaction = WalletTransaction::with('payment')->findOrFail($transactionId);

        // Transaction ownership check
        if ($transaction->source_id !== Auth::id() || $transaction->source_type !== User::class) {
            abort(403);
        }

        // Check status
        if (!$transaction->status->isPending() || (!$transaction->payment || !$transaction->payment->status->isPending())) {
            return to_route('profile.wallet')->with('error-alert', 'این تراکنش دیگر قابل پرداخت نیست.');
        }
        $transaction->update(['created_at' => now()]);

        try {
            $gatewayURL = $paymentService->paymentPage($transaction, $transaction->payment);
            return redirect()->away($gatewayURL);

        } catch (Exception $e) {
            Log::error('Retry Payment Failed', ['message' => $e->getMessage()]);
            return to_route('profile.wallet')->with('error-alert', 'اتصال مجدد به درگاه با خطا مواجه شد. لطفاً بعداً تلاش کنید.');
        }
    }


    /**
     * @param array|null $properties
     * @return void
     */
    public function removeFilters(array $properties = null): void
    {
        if ($properties) {
            $this->reset($properties);
        } else {
            $this->reset(['search', 'type', 'status', 'date']);
        }
        $this->dispatch('resetDatePicker');
    }

    /**
     * @return void
     */
    public function loadMorePersonal(): void
    {
        $this->personalTake += 10;
    }

    /**
     * @return void
     */
    public function loadMoreCompany(): void
    {
        $this->companyTake += 10;
    }

    /*
    |--------------------------------------------------------------------------
    | Private Helper Functions
    |--------------------------------------------------------------------------
    |
    |
    */


    private function myTransactions()
    {
        $date = !empty($this->date) ? Jalalian::fromFormat('Y-m-d', $this->date)->toCarbon() : null;

        return WalletTransaction::where([
            'source_id' => Auth::id(),
            'source_type' => User::class
        ])->when(!empty($this->search), fn($q) => $q->whereLike('amount', "{$this->search}"))
            ->when($this->type, fn($q) => $q->where('type', $this->type))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when(isset($date), fn($q) => $q->whereDate('created_at', $date))
            ->orderByDesc('updated_at')
            ->take($this->personalTake)
            ->get();
    }


    private function companiesTransactions()
    {
        $companyIds = Auth::user()->companies()->pluck('id');
        $date = !empty($this->date) ? Jalalian::fromFormat('Y-m-d', $this->date)->toCarbon() : null;


        return WalletTransaction::where('source_type', Company::class)
            ->whereIn('source_id', $companyIds)
            ->withOnly('wallet')
            ->when(!empty($this->search), fn($q) => $q->whereLike('amount', "{$this->search}")
                ->orWhereHas('source', fn($query) => $query->whereLike('name', "%{$this->search}%")))
            ->when($this->type, fn($q) => $q->where('type', $this->type))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when(isset($date), fn($q) => $q->whereDate('created_at', $date))
            ->take($this->companyTake)
            ->orderByDesc('updated_at')
            ->get();
    }

    private function createTransaction()
    {
        return WalletTransaction::create([
            'type' => 'credit',
            'status' => 'pending',
            'amount' => (int)$this->amount,
            'description' => $this?->description ?? null,
            'wallet_id' => $this->resolveWallet()->id,
            'source_type' => $this->chargeTarget ? Company::class : Auth::user()::class,
            'source_id' => $this->chargeTarget ? $this->companyID : Auth::id()
        ]);
    }

    private function createPayment(WalletTransaction $transaction)
    {
        return $transaction->payment()->create([
            'amount' => (int)$this->amount,
            'gateway' => 'saman',
            'status' => 'pending'
        ]);
    }

    private function resolveWallet(): Wallet
    {
        return $this->chargeTarget
            ? Company::findOrFail($this->companyID)->wallet
            : Auth::user()->wallet;
    }


    /**
     * @param string $amount
     * @return int
     */
    private function convertToInt(string $amount): int
    {
        return (int)str_replace(',', '', $amount);
    }

    /**
     * @return bool
     */
    #[Computed]
    public function hasFilters(): bool
    {
        return $this->search || $this->type || $this->status || $this->date;
    }
}
