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
        if ($this->routeIs('user.store')) {
            $rules = [
                'name' => 'required|min:3|max:255',
                'phone' => 'required|numeric|digits:11|unique:users,phone',
                'status' => 'required|in:0,1',
                'company_id' => 'sometimes|required|numeric|exists:companies,id'
            ];
        } else {
            $rules = [
                'name' => 'required|min:3|max:255',
                'phone' => 'required|numeric|digits:11|unique:users,phone,' . $this->user->id,
                'status' => 'required|in:0,1',
                'company_id' => 'sometimes|required|numeric|exists:companies,id'
            ];
        }

        if (Acl::hasRole(['manager'])) {
            $rules['user_type'] = 'nullable';

        } else {
            $rules['user_type'] = 'required|in:0,1,2,3';

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
}
