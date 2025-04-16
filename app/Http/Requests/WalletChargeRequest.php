<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WalletChargeRequest extends FormRequest
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
            'amount' => 'required|numeric|min:10000|max:50000000',
            'description' => 'nullable|string|min:5',
            'type' => 'required|string|in:credit,debit'
        ];
    }


    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'amount' => str_replace(',', '', $this->amount),
        ]);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'amount' => 'مبلغ',
            'type' => 'نوع تراکنش'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amount.min' => 'مبلغ باید حداقل 10,000 تومان باشد.',
            'amount.max' => 'مبلغ باید حداکثر 50 میلیون تومان باشد.',
        ];
    }
}
