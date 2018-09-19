<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name'          => 'required|string|max:255',
            'legal_name'    => 'required|string|max:255',
            'phone'         => 'required|string|max:50|unique:users,phone',
            'password'      => 'required|string|confirmed',
            'official_data' => 'required|string',
            'requisites'    => 'required|string',
            'role'          => 'required|string',
            'categories'    => 'required_if:role,provider|array',
        ];

        // Update data
        if ($this->isMethod('PUT')) {
            $user_id = array_last($this->segments());

            $rules['phone']    = ['required', Rule::unique('users')->ignore($user_id)];
            $rules['email']    = ['nullable', 'email', Rule::unique('users')->ignore($user_id)];
            $rules['password'] = 'nullable|string|confirmed';
        }

        return $rules;
    }
}
