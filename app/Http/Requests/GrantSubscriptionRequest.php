<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GrantSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'entity_ids' => 'required|array',
            'entity_ids.*' => 'numeric|' . ($this->get('type') === 'user' ? 'exists:users,id' : 'exists:companies,id'),
            'plan' => 'required|numeric|exists:subscription_plans,id',
            'type' => 'string|in:user,company',
            'auto_renew' => 'boolean',
            'withdraw_wallet' => 'bool'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'auto_renew' => (bool)$this->auto_renew,
            'withdraw_wallet' => (bool)$this->withdraw_wallet,
            'entity_ids' => explode(',', $this->entity_ids)
        ]);
    }

    public function attributes()
    {
        return [
            'plan' => 'طرح اشتراک',
            'auto_renew' => 'تمدید خودکار',
            'entity_ids' => $this->get('type') === 'user' ? 'کاربر' : 'سازمان',
            'entity_ids.*' => $this->get('type') === 'user' ? 'کاربر' : 'سازمان',
            'withdraw_wallet' => 'کسر از کیف پول'
        ];
    }
}
