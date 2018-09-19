<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrder extends FormRequest
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
        $rules =  [
            'bag_id'           => 'required|integer',
            'amount'           => 'required|numeric',
            'store_id'         => 'required|integer',
            'goods'            => 'required|array',
            'goods.*.id'       => 'required|integer',
            'goods.*.price'    => 'required|numeric',
            'goods.*.quantity' => 'required|integer',
        ];

        if ($this->isMethod('PUT')) {
            unset($rules['bag_id']);
        }

        return $rules;
    }
}
