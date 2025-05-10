<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\ParameterBag;

class SubscribeRequest extends FormRequest
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
            'plan' => 'required|numeric|exists:subscription_plans,id',
            'auto_renew' => 'boolean'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'auto_renew' => (bool)$this->auto_renew
        ]);
    }

    public function attributes()
    {
        return [
            'plan' => 'طرح اشتراک',
            'auto_renew' => 'تمدید خودکار'
        ];
    }
}
