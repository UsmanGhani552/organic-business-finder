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
            'phone' => 'required|string',
            'email' => 'required|email|unique:farms,email',
            'website' => 'required|url',
            'description' => 'required|string',
            'categories' => 'required|array', 
            'categories.*' => 'exists:categories,id',
            'days' => 'required|array',
            'days.*' => 'exists:days,id',
            'timings' => 'required|string',
            'delivery_option' => 'required|string',
            'payments' => 'required|array', // Array of payment IDs
            'payments.*' => 'exists:payments,id', // Each payment must exist in the payments table
            'image' => 'required|max:2048', // Farm image
            'products' => 'required|array', // Array of products
            'products.*.name' => 'required|string', // Name of each product
            'products.*.price' => 'required|numeric', // Price of each product
            'products.*.image' => 'required|max:2048', // Image of each product
        ];
    }
}
