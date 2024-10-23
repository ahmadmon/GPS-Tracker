<?php

namespace App\Http\Requests;

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
            return [
                'name' => 'required|min:3|max:255',
                'phone' => 'required|numeric|digits:11|unique:users,phone',
                'user_type' => 'required|in:0,1',
                'status' => 'required|in:0,1'
            ];
        }else{
            return [
                'name' => 'required|min:3|max:255',
                'phone' => 'required|numeric|digits:11|unique:users,phone,' . $this->user->id,
                'user_type' => 'required|in:0,1',
                'status' => 'required|in:0,1'
            ];
        }
    }
}
