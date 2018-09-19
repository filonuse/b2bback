<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGoods extends FormRequest
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
        return [
            'category_id'     => 'required|integer',
            'name'            => 'required|string|max:255',
            'brand'           => 'required|string|max:255',
            'description'     => 'required|string|max:512',
            'quantity_actual' => 'required|integer',
            'price'           => 'required|numeric',
            'article'         => 'required|string',
            'country'         => 'required|string',
            'images'          => 'nullable|array|max:8',
            'images.file'     => 'nullable|string',
        ];
    }
}
