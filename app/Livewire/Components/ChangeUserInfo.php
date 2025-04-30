<?php

namespace App\Livewire\Components;

use App\Http\Services\Notify\SMS\SmsService;
use App\Models\User;
use App\Traits\VerificationCode;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\Enums\Position;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ChangeUserInfo extends Component
{
    use VerificationCode;

    #[Validate('required|string|min:3|max:255')]
    public string $name;
    #[Validate('required|numeric|digits:11')]
    public string $phone;
    public array|null $companiesName;

    #[Validate('required|numeric|digits:4|exists:verification_codes,otp')]
    public string $otp_code;
    public bool $show = false;
    public int $duration;

    public function mount(User $user): void
    {
        $this->name = $user->name;
        $this->phone = $user->phone;
        $this->companiesName = $user->companies->isNotEmpty() ? $user->companies->pluck('name')->toArray() : null;

    }

    public function render()
    {
        return view('livewire.components.change-user-info');
    }

    public function store(): void
    {
        $this->validateOnly('name');
        $this->validateOnly('phone');

        $user = User::where('phone', $this->phone)->first();

        if (isset($user) && $user->phone == auth()->user()->phone) {
            auth()->user()->update(['name' => $this->name]);
            $this->showAlert('success', 'اطلاعات شما با موفقیت تغییر یافت.');
            return;

        } elseif (isset($user)) {
            auth()->logout();
            $this->redirectRoute('login');
            return;
        }

        $this->show = true;

        $smsService = new SmsService();
        $otpModel = $this->generateOtp($this->phone, $smsService, auth()->user());
        $seconds = isset($otpModel) ? Carbon::parse($otpModel->expired_at)->diffInSeconds(Carbon::now()) : -180;
        $this->duration = (int)number_format($seconds) * -1;
    }

    public function verify(): void
    {
        $this->validateOnly('otp_code');

        $typeNumber = $this->verifyOtp($this->otp_code, auth()->id());


        match ($typeNumber) {
            '0' => $this->addError('otp_code', 'کد ورود نادرست است.'),
            '1' => $this->addError('otp_code', 'کد ورود منقضی شده است.'),
            '2' => null,
            '3' => $this->changePassword(),
            default => $this->showAlert('error', 'خطایی به وجود آمده است,لطفا بعدا تلاش کنید!'),
        };
    }

    private function changePassword(): void
    {
        auth()->user()->update([
            'name' => $this->name,
            'phone' => $this->phone,
            'phone_verified_at' => now()
        ]);

        $this->reset('show', 'otp_code', 'duration');
        $this->showAlert('success', 'اطلاعات شما با موفقیت تغییر یافت.');

        $this->redirectRoute('profile.index');
    }

    private function showAlert($type, $message): void
    {
        LivewireAlert::title($message)
            ->position(Position::Top)
            ->$type()
            ->toast()
            ->timer(3000)
            ->show();
    }
}
