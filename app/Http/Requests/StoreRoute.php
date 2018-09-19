<?php

namespace App\Http\Requests;

use App\Enums\RoleType;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoute extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->hasRole(RoleType::PROVIDER);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules =  [
            'time_start'            => 'required|date_format:H:i:s',
            'time_finish'           => 'required|date_format:H:i:s',
            'max_deviation'         => 'required|integer',
            'polyline_points'       => 'required|string',
            //Points
            'addresses'             => 'required|array',
            'addresses.*.address'   => 'required|string',
            'addresses.*.lat'       => 'required|numeric',
            'addresses.*.lng'       => 'required|numeric',
            'addresses.*.place_id'  => 'nullable|string',
            //Deviations
            'deviations'            => 'nullable|array',
            'deviations.*.distance' => 'required_with:deviations|integer',
            'deviations.*.price'    => 'required_with:deviations|numeric',
            'deviations.*.percent'  => 'required_with:deviations|numeric',
        ];
        // Update the route
        if ($this->isMethod('PUT')) {
            $rules['polyline_points'] = 'nullable|string';
        }

        return $rules;
    }
}
