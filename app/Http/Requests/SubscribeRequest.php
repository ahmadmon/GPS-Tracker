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
            'is_activated_automatically' => 'boolean'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_activated_automatically' => (bool)$this->is_activated_automatically
        ]);
    }

    public function attributes()
    {
        return [
            'plan' => 'طرح اشتراک',
            'is_activated_automatically' => 'تمدید خودکار'
        ];
    }
}
