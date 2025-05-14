<?php

namespace App\Livewire\Components;

use App\Models\Subscription;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CancellationModal extends Component
{
    public Subscription $subscription;

    #[Validate('bool', as: 'نوع واریز')]
    public bool $refundType = false;
    #[Validate('required_if:refundType,false|nullable|string|regex:/^IR\d{24}$/', as: 'شماره شبا',
        message: [
            'required_if' => 'فیلد :attribute الزامی است.',
            'regex' => ":attribute معتبر نیست! فرمت صحیح: IR به همراه ۲۴ رقم"
        ])]
    public ?string $iban;
    #[Validate('required|string|min:5', as: 'دلیل لغو')]
    public string $reason;


    public function render()
    {
        return view('livewire.components.cancellation-modal');
    }


    public function handleCancellation()
    {
        $inputs = (object)$this->validate();

        dd($inputs);
    }
}
