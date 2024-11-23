<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleRequest extends FormRequest
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
            'license_plate' => 'required|string',
            'user_id' => 'nullable|numeric|exists:users,id',
            'status' => 'required|numeric|in:0,1'
        ];
    }


    public function attributes()
    {
        return [
            'name' => 'نام وسیله نقلیه',
            'license_plate' => 'پلاک',
            'user_id' => 'کاربر'
        ];
    }
}
