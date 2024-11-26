<?php

namespace App\Http\Requests;

use App\Facades\Acl;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $isStoreRoute = $this->routeIs('user.store');
        if ($isStoreRoute) {
            $rules = [
                'name' => 'required|min:3|max:255',
                'password' => 'required|min:8',
                'phone' => 'required|numeric|digits:11|unique:users,phone',
                'status' => 'required|in:0,1',
            ];
        } else {
            $rules = [
                'name' => 'required|min:3|max:255',
                'phone' => 'required|numeric|digits:11|unique:users,phone,' . $this->user->id,
                'password' => 'nullable|min:8',
                'status' => 'required|in:0,1',
            ];
        }

        if (Acl::hasRole(['manager'])) {
            $rules['user_type'] = 'required|numeric|in:0,1,3';
            $rules['company_id'] = $isStoreRoute ? 'required|numeric|exists:companies,id' : 'nullable|numeric|exists:companies,id';

        } else {
            $rules['user_type'] = 'required|numeric|in:0,1,2,3';
            $rules['company_id'] = $isStoreRoute ? 'required_if:user_type,0|required_if:user_type,1|required_if:user_type,3|numeric|exists:companies,id' : 'nullable|numeric|exists:companies,id';

        }

        if (can('user-permissions')) {
            $rules['role'] = 'required|integer|exists:roles,id';
            $rules['permissions'] = 'required|array';

        } else {

            $rules['role'] = 'nullable|integer|exists:roles,id';
            $rules['permissions'] = 'nullable|array';
        }
        $rules['permissions.*'] = 'numeric|exists:permissions,id';

        return $rules;
    }


    public function attributes(): array
    {
        return [
            'phone' => 'شماره تماس',
            'user_type' => 'نوع کاربر',
            'role' => 'نقش',
            'permissions' => 'دسترسی ها',
            'permissions.*' => 'دسترسی ها',
            'company_id' => 'سازمان'
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
            'company_id.required_if' => "فیلد :attribute زمانی که فیلد نوع کاربر برابر با کاربر باشد الزامی است.",
        ];
    }
}
