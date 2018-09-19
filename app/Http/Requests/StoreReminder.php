<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReminder extends FormRequest
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
            'description' => 'required|string|max:191',
            'date_at'     => 'required_without:on_days|date_format:Y-m-d',
            'on_days'     => 'required_without:date_at|array',
            'time_at'     => 'required|date_format:H:i:s',
            'activated'   => 'required',
        ];
    }
}
