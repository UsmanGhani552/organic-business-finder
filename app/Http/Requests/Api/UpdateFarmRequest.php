<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFarmRequest extends FormRequest
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
        // dd($this->route('farm')->id);
        return [
            'name' => 'string',
            'location' => 'string',
            'lat' => 'numeric',
            'lng' => 'numeric',
            'phone' => 'array',
            'email' => 'email|unique:farms,email,'.$this->route('farm')->id,
            'website' => 'url',
            'description' => 'string',
            'categories' => 'array', 
            'categories.*' => 'exists:categories,id',
            'days' => 'required',
            'days.*.day_id' => 'exists:days,id',
            'days.*.timings' => 'string', 
            'days.*.location' => 'string', 
            'days.*.lat' => 'numeric', 
            'days.*.lng' => 'numeric', 
            'timings' => 'string',
            'payments' => 'array', 
            'payments.*' => 'exists:payments,id', 
            'services' => 'array', // of services IDs
            'services.*' => 'exists:services,id', // Each services must exist in the servicess table 
            'delivery_option_id' => 'exists:delivery_options,id',
            'image' => 'image|max:2048', 
            'products' => 'array', 
            'products.*.id' => 'numeric',
            'products.*.name' => 'string',
            'products.*.price' => 'string',
            'products.*.image' => 'image|max:2048',
        ];
    }
}
