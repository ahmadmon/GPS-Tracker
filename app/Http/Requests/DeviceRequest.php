<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeviceRequest extends FormRequest
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
            'name' => 'required|string|min:3|max:255',
            'model' => 'required|string|min:3|max:255',
            'serial' => 'required|numeric|min:10',
            'phone_number' => 'nullable|numeric|digits:11',
            'user_id' => 'required|numeric|exists:users,id',
//            'vehicle_id' => 'required|numeric|exists:vehicles,id',
            'status' => 'required|numeric|in:0,1'
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'نام دستگاه',
            'serial' => 'شماره سریال',
            'model' => 'مدل',
            'phone_number' => 'شماره سیم‌کارت',
            'user_id' => 'خریدار',
        ];
    }
}