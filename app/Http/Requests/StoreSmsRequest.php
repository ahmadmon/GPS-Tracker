<?php

namespace App\Http\Requests;

use App\Enums\DeviceBrand;
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
        $rules = [
            'command' => 'required|in:0,1,2,3,4,5,6,7',
            'apn' => 'nullable|required_if:command,1|string',
            'interval' => 'nullable|required_if:command,2|numeric|min:10',
            'passStatus' => 'nullable|in:false,on',
//            'password' => 'nullable|required_if:command,4|numeric',
            'phones' => 'nullable|required_if:command,5|required_if:command,6|array|max:2',
            'phones.0' => 'nullable|required_if:command,5|required_if:command,6|numeric|digits:11',
            'phones.1' => 'nullable|numeric|digits:11',
        ];
        if ($this->device->brand === DeviceBrand::SINOTRACK) {
            $rules['password'] = 'nullable|required_if:command,4|numeric|digits:4';
        } else {
            $rules['password'] = 'nullable|required_if:command,4|numeric|digits:6';
        }

        return $rules;
    }


    public function attributes(): array
    {
        return [
            'apn' => 'نقطه دستیابی (APN)',
            'interval' => 'زمان',
            'password' => 'رمز عبور',
            'passStatus' => 'وضعیت رمزعبور',
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
            'passStatus.required_if' => "فیلد :attribute الزامی میباشد.",
            'phones.required_if' => "فیلد :attribute الزامی میباشد.",
            'phones.*.required_if' => "فیلد :attribute الزامی میباشد.",
        ];
    }
}
