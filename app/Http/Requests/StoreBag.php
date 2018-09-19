<?php

namespace App\Http\Requests;

use App\Models\Bag;
use Illuminate\Foundation\Http\FormRequest;

class StoreBag extends FormRequest
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
            'goods'       => 'required|integer',
            'quantity'    => 'required|integer',
            'provider_id' => 'required|integer',
        ];

        if ($this->isMethod('PUT'))
            unset($rules['provider_id']);

        return $rules;
    }
}
