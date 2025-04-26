<?php

namespace App\Http\Requests;

use App\Enums\Subscription\Plan\PlanType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscriptionPlanRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|min:5|string',
            'slug' => 'required|string|regex:/^[a-z0-9]+(-[a-z0-9]+)*$/',
            'price' => 'required|numeric',
            'is_lifetime' => 'bool',
            'duration' => 'required_if:is_lifetime,false|numeric|min:1',
            'type' => ['required', Rule::in(PlanType::values())],
            'status' => 'required|numeric|in:0,1'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'price' => str_replace(',', '', $this->price),
            'is_lifetime' => (bool)$this->is_lifetime
        ]);
    }

    public function attributes(): array
    {
        return [
            'slug' => 'نامک (اسلاگ)',
            'is_lifetime' => 'مادام‌العمر',
            'duration' => 'مدت اعتبار',
        ];
    }

    public function messages(): array
    {
        return [
            'duration.required_if' => 'لطفاً مدت اعتبار را وارد کنید.',
            'duration.min' => 'مدت اعتبار باید حداقل 1 روز باشد.',
            'slug.regex' => ":attribute باید فقط شامل حروف کوچک انگلیسی، اعداد و خط تیره باشد و از خط تیره در ابتدا یا انتها استفاده نشود.",
            'type.in' => 'نوع های مجاز (شخصی,سازمانی,عمومی) میباشد.'
        ];
    }
}
