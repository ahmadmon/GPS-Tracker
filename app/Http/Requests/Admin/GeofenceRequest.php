<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GeofenceRequest extends FormRequest
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
            'description' => 'nullable|string|min:10',
            'status' => 'required|numeric|in:0,1',
            'type' => 'required|numeric|in:0,1',
            'device_id' => 'required|numeric|exists:devices,id',
            'geometry' => 'required|json',
            'time_restriction' => 'nullable',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
        ];
    }

    public function attributes(): array
    {
        return [
            'type' => 'نوع حصار',
            'device_id' => 'دستگاه ردیاب',
            'geometry' => 'حصار جغرافیایی',
            'time_restriction' => 'محدودیت زمانی',
            'start_time' => 'زمان شروع',
            'end_time' => 'زمان پایان',
        ];
    }
}
