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
            'command' => 'required|in:0,1,2,3,4,5,6,7,8',
            'apn' => 'nullable|required_if:command,1|string',
            'interval' => 'nullable|required_if:command,2|numeric|min:10',
            'passStatus' => 'nullable|in:false,on',
            //            'password' => 'nullable|required_if:command,4|numeric',
        ];
        if ($this->device->brand === DeviceBrand::SINOTRACK) {
            $rules['phones.0'] = 'nullable|required_if:command,4|numeric|digits:11';
            $rules['password'] = 'nullable|required_if:command,3|numeric|digits:4';
            $rules['other'] = 'nullable|required_if:command,8|string|regex:/^[A-Za-z0-9,]+$/';
            $rules['mode'] = 'nullable|required_if:command,6|string|in:WORK,MOVE,STANDBY';
        } else {
            $rules['phones'] = 'nullable|required_if:command,5|required_if:command,6|array|max:2';
            $rules['phones.0'] = 'nullable|required_if:command,5|required_if:command,6|numeric|digits:11';
            $rules['phones.1'] = 'nullable|numeric|digits:11';
            $rules['password'] = 'nullable|required_if:command,4|numeric|digits:6';
            $rules['other'] = 'nullable|required_if:command,8|string|regex:/^[A-Za-z0-9,]+$/';
        }

        return $rules;
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'apn' => 'نقطه دستیابی (APN)',
            'interval' => 'زمان',
            'password' => 'رمز عبور',
            'passStatus' => 'وضعیت رمزعبور',
            'phones' => 'شماره تماس ادمین',
            'phones.*' => 'شماره تماس ادمین',
            'other' => 'سایر دستورات',
            'mode' => 'حالت عملکرد'
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
            'other.required_if' => "فیلد :attribute الزامی میباشد.",
            'mode.required_if' => "فیلد :attribute الزامی میباشد.",
            'other.regex' => 'مقدار وارد شده باید فقط شامل حروف و اعداد انگلیسی، و علامت کاما (,) باشد. استفاده از سایر عبارات مجاز نیست.',
        ];
    }
}
