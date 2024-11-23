<?php

namespace App\Http\Requests;

use App\Facades\Acl;
use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
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
        if ($this->routeIs('company.store')) {

            $rules = [
                'name' => 'required|string|min:3|max:255',
                'bio' => 'nullable|string|min:10',
                'contact_number' => 'required|numeric|digits:11|unique:companies',
                'logo' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:2048',
                'address' => 'required|string|min:10',
                'status' => 'required|numeric|in:0,1'
            ];
        } else {
            $rules = [
                'name' => 'required|string|min:3|max:255',
                'bio' => 'nullable|string|min:10',
                'contact_number' => 'required|numeric|digits:11|unique:companies,contact_number,' . $this->company->id,
                'logo' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:2048',
                'address' => 'required|string|min:10',
                'status' => 'required|numeric|in:0,1'
            ];
        }

        if (Acl::hasRole(['manager'])) {
            $rules['user_id'] = 'nullable';
        } else {
            $rules['user_id'] = 'required|numeric|exists:users,id';
        }

        return $rules;
    }


    public function attributes(): array
    {
        return [
            'bio' => 'بیو (توضیحات)',
            'contact_number' => 'شماره تماس',
            'user_id' => 'مدیر سازمان'
        ];
    }
}
