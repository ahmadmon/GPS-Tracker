<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSmsRequest extends FormRequest
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
            'command' => 'required|in:0,1,2,3,4,5,6',
            'apn' => 'nullable|required_if:command,1|string',
            'interval' => 'nullable|required_if:command,2|numeric|min:10',
            'password' => 'nullable|required_if:command,3|numeric|digits:4',
            'phones' => 'nullable|required_if:command,4,5|array|max:2',
            'phones.0' => 'nullable|required_if:command,4,5|numeric|digits:11',
            'phones.1' => 'nullable|numeric|digits:11',
        ];
    }


    public function attributes(): array
    {
        return [
            'apn' => 'نقطه دستیابی (APN)',
            'interval' => 'زمان',
            'password' => 'رمز عبور',
            'phones' => 'شماره تماس ادمین',
            'phones.*' => 'شماره تماس ادمین'
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
            'apn.required_if' => "فیلد :attribute الزامی میباشد.",
            'interval.required_if' => "فیلد :attribute الزامی میباشد.",
            'password.required_if' => "فیلد :attribute الزامی میباشد.",
            'phones.required_if' => "فیلد :attribute الزامی میباشد.",
            'phones.*.required_if' => "فیلد :attribute الزامی میباشد.",
        ];
    }
}
