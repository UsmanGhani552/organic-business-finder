<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreFarmRequest extends FormRequest
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
        return [
            'name' => 'required|string',
            'location' => 'required|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'phone' => 'required|array',
            'email' => 'required|email|unique:farms,email',
            'website' => 'required|url',
            'description' => 'required|string',
            'categories' => 'required|array', 
            'categories.*' => 'required|exists:categories,id',
            'days' => 'required',
            'days.*.day_id' => 'required|exists:days,id',
            'days.*.timings' => 'required', 
            'days.*.location' => 'required', 
            'days.*.lat' => 'required', 
            'days.*.lng' => 'required', 
            'timings' => 'required|string',
            'payments' => 'required|array', // of payment IDs
            'payments.*' => 'required|exists:payments,id', // Each payment must exist in the payments table 
            'services' => 'required|array', // of services IDs
            'services.*' => 'required|exists:services,id', // Each services must exist in the servicess table 
            'delivery_option_id' => 'required|exists:delivery_options,id',  
            'image' => 'required', // Farm image
            'products' => 'required', // of products
            'products.*.name' => 'required|string', // Name of each product
            'products.*.price' => 'required',
            'products.*.image' => 'required', 
        ];
    }
}
