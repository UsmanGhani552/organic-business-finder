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
            'name' => 'required|string',
            'location' => 'required|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'phone' => 'required|string',
            'email' => 'required|email|unique:farms,email,'.$this->route('farm')->id,
            'website' => 'required|url',
            'description' => 'required|string',
            'categories' => 'required|array', 
            'categories.*' => 'exists:categories,id',
            'days' => 'required|array', 
            'days.*' => 'exists:days,id', 
            'timings' => 'required|string',
            'delivery_option' => 'required|string',
            'payments' => 'required|array', 
            'payments.*' => 'exists:payments,id', 
            'image' => 'image|max:2048', 
            'products' => 'required|array', 
            'products.*.name' => 'required|string',
            'products.*.price' => 'required',
            'products.*.image' => 'image|max:2048',
        ];
    }
}
